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

    public function setConnexion($conn)
    {
        $this->conn = $conn;
    }

    public function getClassName()
    {
        return (new \ReflectionClass($this))->getShortName();
    }

    public function arrayToObject(Array $data)
    {
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = new $this->entityName($data[$i]);
        }
        return $data;
    }

    public function getById(Int $id)
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

    public function getAll()
    {
        $qb = $this->conn->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->getClassName())
            ->execute()
            ->fetchAll();

        return $this->arrayToObject($data);
    }

    public function getAllBy(String $property, String $order)
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

    public function count() : Int
    {
        $qb = $this->conn->createQueryBuilder();
        $count = $qb
            ->select('*')
            ->from($this->getClassName())
            ->execute()
            ->rowCount();
        return $count;
        
    }

    public function getAttributes()
    {
        $columns = [];
        $qb = $this->conn;
        $table = $this->getClassName();
        $data = $qb->query("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = '$table'")->fetchAll();
        foreach($data as $key=>$value){
            array_push($columns, $value['COLUMN_NAME']);
        }
        return $columns;
    }
}
