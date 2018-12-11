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

        $this->checkDB();
    }

    public static function init()
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

    private function checkDB()
    {
        $pdo = $this->init();
        try {
            $query = $pdo->query("SELECT 1 FROM phpstorm_migrations LIMIT 1;");
        } catch (\PDOException $e) {
            if ($e->getCode() == '42S02') {
                $pdo->query("CREATE TABLE `la_mer_noire`.`phpstorm_migrations` ( `id` INT NOT NULL AUTO_INCREMENT , `name` VARCHAR(255) NOT NULL , `batch` INT NOT NULL , `migration_time` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;");
            }
        }
    }

    public function new(string $object_name)
    {
        $upper_name = substr_replace($object_name, strtoupper(substr($object_name, 0, 1)), 0, 1);
        require(__DIR__.'/../examples/Entities/'. $upper_name .'.php');
        return new $upper_name;
    }    
}