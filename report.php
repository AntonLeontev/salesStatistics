<?php

use src\DatabaseConfig;
use src\DatabaseHandler;
use src\Logger;
use src\SalesReportBuilder;

include_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$config = new DatabaseConfig();
$pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
$dbHandler = new DatabaseHandler($pdo);
$logger = new Logger();
$reportBuilder = new SalesReportBuilder($pdo);

try {
    $report = $reportBuilder->build();
    $logger->logToTelegram($report);
} catch (Exception $e) {
    $logger->logToTelegram($e->getMessage());
}
