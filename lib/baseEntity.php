<?php

namespace ORM;
use ORM\phpstORM;

abstract class baseEntity {
    public $entityName;
    private $conn;
    private $id;

    public function __construct($data = null)
    {        
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
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
        return $object;
    }

    public function setConnexion($conn): void
    {
        $this->conn = $conn;
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
            
        }
        return $columns;
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function arrayToObject(Array $data): Array
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = new $this->entityName($data[$i]);
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
        return new $this->entityName($data);
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
}
