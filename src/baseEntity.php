<?php

require_once('phpstORM.php');

abstract class baseEntity {
    private $pdo;
    public $entityName;
    protected $id;

    public function __construct($data = null)
    {
        $this->pdo = phpstORM::init();
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $this->{$key} = $value;
            }
        }
        
    }

    public function getById(Int $id)
    {
        $data = $this->pdo->query("SELECT * FROM $this->entityName WHERE id = $id")->fetch();
        return new $this->entityName($data);
    }

    public function getAll()
    {
        $data = $this->pdo->query("SELECT * FROM $this->entityName")->fetchAll();
        for ($i = 0; $i < count($data); $i++) {
            $data[$i] = new $this->entityName($data[$i]);
        }
        return $data;
    }
}