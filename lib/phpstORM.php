<?php

namespace ORM;

use \Doctrine\DBAL\Driver\Connection;

class phpstORM {

    public $conn;
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

    public function init($conn)
    {
        $this->conn = $conn;
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

    public function new($className, $data = null)
    {
        $item = new $className($data);
        $item->setConnexion($this->conn);
        return $item;
    }  
}
