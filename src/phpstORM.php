<?php

class phpstORM {

    public $migrations_folder;

    public function __construct()
    {
        global $config;
        $this->migrations_folder = $config['migrations']['folder'];
        if (substr($this->migrations_folder, 0, 1) != '/') {
            $this->migrations_folder = '/' . $this->migrations_folder;
        }
        if (substr($this->migrations_folder, -1, 1) != '/') {
            $this->migrations_folder = $this->migrations_folder . '/';
        }
    }

    public function init()
    {
        global $config;
        $dsn = 'mysql:dbname='.$config['db']['name'].';host='.$config['db']['host'];
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        try {
            return new PDO($dsn, $config['db']['user'], $config['db']['password'], $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        } 
    }
}