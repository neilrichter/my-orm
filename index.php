<?php
require_once('vendor/autoload.php');
require_once("src/phpstORM.php");
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('config/parameters.yml');

$phpstORM = new phpstORM();

$conn = $phpstORM->init();

$kebab = $phpstORM->new("kebab");

echo "\nGet by ID (1)\n";
var_dump($kebab->getById(1));

echo "\nGet all Kebabs\n";
var_dump($kebab->getAll());

// var_dump($kebab);