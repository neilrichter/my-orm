<?php

require_once('phpstORM.php');

abstract class baseEntity {
    public $entityName;
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
        return $object;
    }

    public function getById(Int $id)
    {
        $qb = phpstORM::init()->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->entityName)
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute()
            ->fetch();
        return new $this->entityName($data);
    }

    public function getAll()
    {
        $qb = phpstORM::init()->createQueryBuilder();
        $data = $qb
            ->select('*')
            ->from($this->entityName)
            ->execute()
            ->fetchAll();

        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = new $this->entityName($data[$i]);
        }
        return $data;
    }
}
