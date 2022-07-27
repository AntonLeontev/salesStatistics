<?php

use src\DatabaseConfig;
use src\DatabaseHandler;
use src\SalesReportBuilder;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

include_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$config = new DatabaseConfig();
$pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
$dbHandler = new DatabaseHandler($pdo);
$reportBuilder = new SalesReportBuilder($pdo);

$loader = new FilesystemLoader(__DIR__ . '/templates');
$twig = new Environment($loader);

if ($_GET['page'] === 'requests') {
    $base = $dbHandler->readRawData(DatabaseHandler::DESC);
    echo $twig->render('base.html.twig', ['text'=>$base]);
}

if ($_GET['page'] === 'orders') {
    $base = $dbHandler->getOrders();
    $report = $reportBuilder->build();

    echo $twig->render('orders.html.twig', ['text'=>$base, 'report'=>$report]);
}
