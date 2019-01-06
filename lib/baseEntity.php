<?php

namespace ORM;
use ORM\phpstORM;

set_error_handler(function($errno, $errstr, $errfile, $errline ){
    throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
});

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

    public function setConnexion($settings): void
    {
        $this->conn = $settings['conn'];
        $this->phpstORM->init($settings);
        
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
        try {
            $qb = $this->conn->createQueryBuilder();
            $query = $qb
                ->select('*')
                ->from($this->getClassName())
                ->where('id = :id')
                ->setParameter('id', $id);

            $queryString = $query->getSql();
            $params = $query->getParameters();

            $start = microtime(true);
            $data = $query->execute();
            $end = microtime(true);

            $data = $data->fetch();

            foreach ($params as $param => $value) {
                $queryString = str_replace(":$param", $value, $queryString);
            }
            $time = round(($end - $start), 4) . ' seconds';
            $this->phpstORM->log('success', "getById - ($id) - \"$queryString\" - $time");
            return $this->phpstORM->new($this->getClass() ,$data);
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "getById - " . $e);
        }
    }

    public function getAll(): Array
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $query = $qb
                ->select('*')
                ->from($this->getClassName());

            $queryString = $query->getSql();
            $start = microtime(true);
            $data = $query
                ->execute();
            $end = microtime(true);
            $data = $data->fetchAll();

            $time = round(($end - $start), 4) . ' seconds';
            $this->phpstORM->log('success', "getAll - \"$queryString\" - $time");

            $data = $this->arrayToObject($data);
            return $data;

        } catch(\ErrorException $e) {
            $this->phpstORM->log('error', "getAll - " . $e);
        }
    }

    public function getAllBy(String $property, String $order): Array
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $query = $qb
                ->select('*')
                ->from($this->getClassName())
                ->orderBy($property, $order);

            $queryString = $query->getSql();
            
            $start = microtime(true);
            $data = $query->execute();
            $end = microtime(true);
            $time = round(($end - $start), 4) . ' seconds';
            $this->phpstORM->log('success', "getAllBy - \"$queryString\" - $time");

            $data = $data->fetchAll();

            return $this->arrayToObject($data);
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "getAllBy - " . $e);
        }
    }

    public function count(): Int
    {
        try {
            $qb = $this->conn->createQueryBuilder();
            $query = $qb
                ->select('id')
                ->from($this->getClassName());

            $queryString = $query->getSql();
            
            $start = microtime(true);
            $count = $query->execute();
            $end = microtime(true);
            $time = round(($end - $start), 4) . ' seconds';

            $this->phpstORM->log('success', "count - \"$queryString\" - $time");

            return $count->rowCount();
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "count - " . $e);
        }
    }

    /**
     * Checks if a value exists with the given data
     * @param QueryBuilder|Array $mixed Querybuilder created by the user or an associative array colum => value
     * @return bool
     */
    public function existsWith($mixed): Bool
    {
        try {
            $this->getQueryBuilder();
            if ($this->isQuery($mixed)) {
                $query = $mixed;
            } else {
                $query = $this->ExistsWithArray($mixed);
            }

            $params = $query->getParameters();
            $queryString = $query->getSql();
            
            foreach ($params as $param => $value) {
                $value = gettype($value) != 'boolean' ? $value : $value ? 'true' : 'false';
                $queryString = str_replace($param, $value, $queryString);
            }

            $start = microtime(true);
            $query = $query->execute();
            $end = microtime(true);
            $time = round(($end - $start), 4) . ' seconds';

            $this->phpstORM->log('success', "existsWith - \"$queryString\" - $time");

            $exists = $query->fetch();
            return !!$exists;
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "existsWith - " . $e);
        }
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
        try {
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
    
            $queryString = $query->getSql();
            $params = $query->getParameters();
            $start = microtime(true);
            $query = $query->execute();
            $end = microtime(true);
    
            $time = round(($end - $start), 4) . ' seconds';
            foreach ($params as $param => $value) {
                $value = gettype($value) != 'boolean' ? $value : $value ? 'true' : 'false';
                $queryString = str_replace($param, $value, $queryString);
            }
    
            $this->phpstORM->log('success', "selectAllWith - \"$queryString\" - $time");
    
            $rows = $query->fetchAll();
            return $this->arrayToObject($rows);
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "selectAllWith - " . $e);
        }
    }

    private function convertValue($property, $value, $types)
    {
        switch ($types[$property]) {
            case 'tinyint':
                return $value === true ? 1 : 0;

            case 'int':
                return intval($value);

            case 'datetime':
                return new \DateTime($value);
            
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
            try {
                $query = $this->getQueryBuilder()
                    ->insert($this->getClassName())
                    ->values($properties);
                foreach ($properties as $property => $value) {
                    $query->setParameter(":$property", $values[$property]);
                }
                $queryString = $query->getSql();
                $params = $query->getParameters();
                foreach ($params as $param => $value) {
                    $queryString = str_replace($param, $value, $queryString);
                }
                $start = microtime(true);
                $query->execute();
                $end = microtime(true);
                $time = round(($end - $start), 4) . ' seconds';

                $this->phpstORM->log('success', "save (insert) - \"$queryString\" - $time");

                $this->id = intval($this->conn->lastInsertId());
            } catch (\ErrorException $e) {
                $this->phpstORM->log('error', "save (insert) - " . $e);
            }
        } else {
            try {
                $query = $this->getQueryBuilder();
                $query
                    ->update($this->getClassName())
                    ->where("id = $this->id");
                foreach ($values as $key => $value) {
                    $query->set($key, "'$value'");
                }

                $queryString = $query->getSql();
                $start = microtime(true);
                $query->execute();
                $end = microtime(true);
                $time = round(($end - $start), 4) . ' seconds';

                $this->phpstORM->log('success', "save (update) - \"$queryString\" - $time");
            } catch (\ErrorException $e) {
                $this->phpstORM->log('error', "save (update) - " . $e);
            }
        }
    }

    public function delete(): void
    {
        if (is_null($this->id)) {
            throw new \Exception("Can't delete a non-existent item");
        }
        try {
            $query = $this->getQueryBuilder()
                ->delete($this->getClassName())
                ->where("id = $this->id");
            
            $queryString = $query->getSql();
            $start = microtime(true);
            $query->execute();
            $end = microtime(true);
            $time = round(($end - $start), 4) . ' seconds';
            $this->phpstORM->log('success', "delete - \"$queryString\" - $time");
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "delete - " . $e);
        }
    }

    public function deleteWith(Array $values): void
    {
        try {
            $query = $this->getQueryBuilder()
                ->delete($this->getClassName());

            foreach ($values as $key => $value) {
                $value = gettype($value) != 'boolean' ? $value : $value ? 'true' : 'false';
                $query->andWhere("$key = $value");
            }
            
            $queryString = $query->getSql();
            $start = microtime(true);
            $query->execute();
            $end = microtime(true);
            $time = round(($end - $start), 4) . ' seconds';
            $this->phpstORM->log('success', "deleteWith - \"$queryString\" - $time");
        } catch (\ErrorException $e) {
            $this->phpstORM->log('error', "deleteWith - " . $e);
        }
    }
}
