<?php

use \Doctrine\DBAL\Driver\Connection;

class phpstORM {

    public $migrations_folder;

    public function __construct()
    {
        // global $params;
        // $this->migrations_folder = $params['migrations']['folder'];
        // if (substr($this->migrations_folder, 0, 1) != '/') {
        //     $this->migrations_folder = '/' . $this->migrations_folder;
        // }
        // if (substr($this->migrations_folder, -1, 1) != '/') {
        //     $this->migrations_folder = $this->migrations_folder . '/';
        // }

        // $this->checkDB();
    }

    public static function init()
    {
        global $params;
        $config = $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = [
            'dbname' => $params['db']['name'],
            'user' => $params['db']['user'],
            'password' => $params['db']['password'],
            'host' => $params['db']['host'],
            'driver' => $params['db']['driver'],
            'charset' => $params['db']['charset'],
        ];
        $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);

        return $conn;
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
