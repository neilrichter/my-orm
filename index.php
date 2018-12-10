<?php
require_once('vendor/autoload.php');
use Symfony\Component\Yaml\Yaml;

$config = Yaml::parseFile('config/parameters.yml');
require("src/phpstORM.php");

$phpstORM = new phpstORM();

$conn = $phpstORM->init();

// $manager = $phpstORM->getManager($conn);
// $manager->newModel("Kebab");
