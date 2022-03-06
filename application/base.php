<?php

use src\DatabaseConfig;
use src\DatabaseHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include_once ('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$config = new DatabaseConfig();
$pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
$dbHandler = new DatabaseHandler($pdo);

$base = $dbHandler->readRawData(DatabaseHandler::DESC);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

echo $twig->render('base.html.twig', ['text'=>$base]);
