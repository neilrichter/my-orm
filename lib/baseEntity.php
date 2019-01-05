<?php

namespace ORM;
use ORM\phpstORM;

abstract class baseEntity {
    public $entityName;
    private $conn;
    private $id;
    private $tempData;
    private $phpstORM;

    public function __construct($data = null)
    {
        $this->phpstORM = new phpstORM;
        if (!is_null($data)) {
            $this->tempData = $data;
        }
    }

    private function assignValues($data)
    {
        $types = [];

        foreach ($this->getAttributes() as $attribute => $type) {
            $types[$attribute] = $type;
        }

        foreach ($data as $key => $value) {
            switch ($types[$key]) {
                case 'tinyint':
                    $this->{$key} = $value == '1';
                    break;

                case 'int':
                    $this->{$key} = intval($value);
                    break;
                
                default:
                    $this->{$key} = $value;
                    break;
            }
        }
    }

    public function __debugInfo()
    {
        $object = [];
        foreach ($this as $key => $value) {
            $object[$key] = $value;
        }
        unset($object['entityName']);
        unset($object['conn']);
        unset($object['phpstORM']);
        return $object;
    }

    public function __set($name, $value)
    {
        if ($name == 'id') {
            throw new \Exception('Cannot edit or set ID manually.');
        }

        $this->{$name} = $value;
    }

    public function setConnexion($conn): void
    {
        $this->conn = $conn;
        $this->phpstORM->init($this->conn);
        
        if (!is_null($this->tempData)) {
            $this->assignValues($this->tempData);
            unset($this->tempData);
        }
    }

    public function getQueryBuilder(): \Doctrine\DBAL\Query\QueryBuilder
    {
        return $this->conn->createQueryBuilder();
    }

    public function getAttributes(): Array
    {
        $columns = [];
        $qb = $this->conn;
        $table = $this->getClassName();
        $data = $qb->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'")->fetchAll();
        foreach($data as $key=>$value){
            $columns[$value['COLUMN_NAME']] = $value['DATA_TYPE'];
            // $columns[$value['COLUMN_NAME']] = [
            //     'TYPE' => $value['DATA_TYPE'],
            //     'IS_NULLABLE' => $value['IS_NULLABLE'],
            // ];
            
        }
        return $columns;
    }

    public function getClassName(): string
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    private function getClass(): string
    {
        return (new \ReflectionClass($this))->getName();
    }

    public function arrayToObject(Array $data): Array
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = $this->phpstORM->new($this->getClass() ,$data[$i]);
        }
        
        return $data;
    }

    private function isQuery($mixed): Bool
    {
        if (gettype($mixed) == 'array') {
            return false;
        } elseif ((new \ReflectionClass($mixed))->getShortName() == 'QueryBuilder') {
            return true;
        } else {
            throw new Exception('Invalid value type');
        }
    }

    public function getById(Int $id): self
    {
        $qb = $this->conn->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->getClassName())
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch();
        return $this->phpstORM->new($this->getClass() ,$data);
    }

    public function getAll(): Array
    {
        $qb = $this->conn->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->getClassName())
            ->execute()
            ->fetchAll();

        return $this->arrayToObject($data);
    }

    public function getAllBy(String $property, String $order): Array
    {
        $qb = $this->conn->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->getClassName())
            ->orderBy($property, $order)
            ->execute()
            ->fetchAll();
        
        return $this->arrayToObject($data);
    }

    public function count(): Int
    {
        $qb = $this->conn->createQueryBuilder();
        $count = $qb
            ->select('id')
            ->from($this->getClassName())
            ->execute()
            ->rowCount();
        return $count;
    }

    /**
     * Checks if a value exists with the given data
     * @param QueryBuilder|Array $mixed Querybuilder created by the user or an associative array colum => value
     * @return bool
     */
    public function existsWith($mixed): Bool
    {
        $this->getQueryBuilder();
        if ($this->isQuery($mixed)) {
            $query = $mixed;
        } else {
            $query = $this->ExistsWithArray($mixed);
        }
        $exists = $query->execute()->fetch();
        return !!$exists;
    }

    private function ExistsWithArray(Array $datas): \Doctrine\DBAL\Query\QueryBuilder
    {
        $qb = $this->conn->createQueryBuilder();
        $qb
            ->select('id')
            ->from($this->getClassName());

        foreach ($datas as $key => $value) {
            $qb->andWhere("$key = :$key");
            $qb->setParameter(":$key", $value);
        }
        return $qb;
    }

    public function selectAllWith($mixed): Array
    {
        if ($this->isQuery($mixed)) {
            $query = $mixed;
            $query->from($this->getClassName());
        } else {
            $query = $this->getQueryBuilder();
            $query
                ->select('*')
                ->from($this->getClassName());

            foreach ($mixed as $key => $value) {
                $query->andWhere("$key = :$key");
                $query->setParameter(":$key", $value);
            }
        }

        $rows = $query->execute()->fetchAll();
        return $this->arrayToObject($rows);
    }

    private function convertValue($property, $value, $types)
    {
        switch ($types[$property]) {
            case 'tinyint':
                return $value === true ? 1 : 0;

            case 'int':
                return intval($value);

            default:
                return $value;
        }
    }

    public function save(): void
    {
        $types = [];
        $properties = [];
        $values = [];

        foreach ($this->getAttributes() as $attribute => $type) {
            $properties[$attribute] = ":$attribute";
            $types[$attribute] = $type;
        }

        unset($properties['id']);

        foreach($properties as $property => $value) {
            $values[$property] = $this->convertValue($property, $this->{$property}, $types);
        }

        if (is_null($this->id)) {
            $query = $this->getQueryBuilder()
                ->insert($this->getClassName())
                ->values($properties);
            foreach ($properties as $property => $value) {
                $query->setParameter(":$property", $values[$property]);
            }
            $query->execute();
            $this->id = intval($this->conn->lastInsertId());
        } else {
            $this->conn->update($this->getClassName(), $values, ['id' => $this->id]);
        }
    }

    public function delete(): void
    {
        if (is_null($this->id)) {
            throw new \Exception("Can't delete a non-existent item");
        }
        $this->conn->delete($this->getClassName(), ['id' => $this->id]);
    }

    public function deleteWith(Array $values): void
    {
        $this->conn->delete($this->getClassName(), $values);
    }
}
