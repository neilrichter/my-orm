<?php

namespace ORM;

use \Doctrine\DBAL\Driver\Connection;

class phpstORM {

    private $logFolder;
    public $settings;
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

    public function init($settings)
    {
        $this->settings = $settings;
        if (!file_exists(__DIR__ . '/../var/' . $settings['log_folder'])) {
            mkdir(__DIR__ . '/../var/' . $settings['log_folder'], 0777, true);
        }
        $this->logFolder = __DIR__ . '/../var/' . $settings['log_folder'] . '/';

        if (!file_exists($this->logFolder . 'access.log')) {
            touch($this->logFolder . 'access.log');
        }
        if (!file_exists($this->logFolder . 'error.log')) {
            touch($this->logFolder . 'error.log');
        }
    }

    // string $type, $content
    public function log(string $type, $content = '')
    {
        $content .= "\n";
        $hour = (new \DateTime())->format('[d/m/Y H:i:s] ');
        $content = $hour . $content;
        switch ($type) {
            case 'success':
                return file_put_contents($this->logFolder . 'access.log', $content, FILE_APPEND);
            
            case 'error':
                return file_put_contents($this->logFolder . 'error.log', $content, FILE_APPEND);
            default:
                # code...
                break;
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

    public function new($className, $data = null)
    {
        $item = new $className($data);
        $item->setConnexion($this->settings);
        return $item;
    }  
}
