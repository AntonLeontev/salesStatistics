<?php

use src\DatabaseConfig;
use src\DatabaseHandler;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include_once ('vendor/autoload.php');

$config = new DatabaseConfig(__DIR__ . '/dbconnect');
$pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
$dbHandler = new DatabaseHandler($pdo);

$base = $dbHandler->readAll();

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

echo $twig->render('base.html.twig', ['text'=>$base]);
