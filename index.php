<?php
require_once('vendor/autoload.php');
require_once("src/phpstORM.php");
use Symfony\Component\Yaml\Yaml;

$params = Yaml::parseFile('config/parameters.yml');

$phpstORM = new phpstORM();

$conn = $phpstORM->init();

$kebab = $phpstORM->new("kebab");

echo "\nGet by ID (1)\n";
var_dump('<pre>', $kebab->getById(1));

echo "\nGet all Kebabs\n";
var_dump('<pre>', $kebab->getAll());

echo "\n Get all Kebabs second time\n";
var_dump('<pre>', $kebab->getAttributes());
