<?php
require_once('../vendor/autoload.php');

use ORM\phpstORM;
use Symfony\Component\Yaml\Yaml;

$params = Yaml::parseFile('../config/parameters.yml');

$phpstORM = new phpstORM();

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

$phpstORM = new phpstORM();
$phpstORM->init($conn);

$kebab = $phpstORM->new('Kebab');

// echo "\nGet by ID (1)\n";
// var_dump('<pre>', $kebab->getById(1));

// echo "\nGet all Kebabs\n";
// var_dump('<pre>', $kebab->getAll());

// echo "\n Get all Kebabs second time\n";
// var_dump('<pre>', $kebab->getAttributes());

// echo "\n Get all Kebabs by\n";
// var_dump('<pre>', $kebab->getAllBy('name', 'DESC'));

// echo "\nCount Kebabs\n";
// echo $kebab->count();

/*
 * existsWith can take either an associative array either a querybuilder
 * for complex requests
 * /!\ stop the query before the execution step. /!\
echo "\nKebab exists ?\n";
$kebabQB = $kebab->getQueryBuilder();
$kebabQB
    ->select('*')
    ->from($kebab->getClassName())
    ->where('tomate = :tomate')
    ->setParameter(':tomate', false)
    ->andWhere('oignon = :oignon')
    ->setParameter(':oignon', false);
echo $kebab->existsWith($kebabQB);
echo $kebab->existsWith([
    'tomate' => !"1",
    'salade' => true,
    'oignon' => true,
]);
*/

echo "\Select all kebabks with \n";
$kebabQB = $kebab->getQueryBuilder();
$kebabQB
    ->select('*')
    ->where('tomate = :tomate')
    ->setParameter(':tomate', true);
var_dump($kebab->selectAllWith($kebabQB));
// var_dump($kebab->selectAllWith([
//     'tomate' => true,
//     // 'salade' => true,
//     // 'oignon' => true,
// ]));
