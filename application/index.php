<?php

use src\DatabaseConfig;
use src\Logger;
use src\QueryHandler;
use src\DatabaseHandler;
use Webmozart\Assert\Assert;

include_once ('vendor/autoload.php');
try {
    $logger = new Logger();
    $logger->logString('test');
    $config = new DatabaseConfig(__DIR__ . '/dbconnect');
    $pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
    $dbHandler = new DatabaseHandler($pdo);
    $queryHandler = new QueryHandler();

    $result = $queryHandler->handleGet($_GET);

    Assert::stringNotEmpty($result);
    $dbHandler->write($result);
} catch (Error $e) {
    $logger = new Logger();
    $logger->log($e);
}

