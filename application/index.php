<?php

use src\DatabaseConfig;
use src\Designer;
use src\Logger;
use src\Order;
use src\QueryHandler;
use src\DatabaseHandler;

include_once ('vendor/autoload.php');
try {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    $config = new DatabaseConfig();
    $pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
    $dbHandler = new DatabaseHandler($pdo);
    $queryHandler = new QueryHandler();
    $logger = new Logger();

    $query = $queryHandler->handleGet($_GET);
    $dbHandler->writeRawData($query);
    $logger->logString();

    //-----Order--------------
    $parsedData = $queryHandler->parseQuery($query);
    $order = new Order($parsedData);
    $orderId = $dbHandler->addOrder($order);
    $logger->logString($order);

    if ($order->getDesigner()) {
        $designer = new Designer($order->getDesigner());
        $designerId = $dbHandler->addDesigner($designer);
        $dbHandler->addDesignerInOrder($orderId, $designerId);
        $logger->logString("New $designer");
    }

} catch (Error|Exception $e) {
    if ($e->getMessage() === 'Filter empty queries') {
        exit();
    }
    $logger = new Logger();
    $logger->logError($e);
}

