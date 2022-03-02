<?php

use src\DatabaseConfig;
use src\Designer;
use src\Exceptions\DatabaseHandlerException;
use src\Logger;
use src\Order;
use src\QueryHandler;
use src\DatabaseHandler;
use Webmozart\Assert\Assert;

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

    Assert::stringNotEmpty($query, 'Filter empty queries');
    Assert::regex($query, '~[aAаА][- ]?\d{6}-\d{1,2}~');

    if (!$dbHandler->write($query)) {
        throw new DatabaseHandlerException("Query not added to DB: $query");
    }
    $logger->logString();

    $order = new Order($query);

    Assert::stringNotEmpty($order->getDesigner());

    $designer = new Designer($order->getDesigner());
    if (!$dbHandler->addDesigner($designer)) {
        throw new DatabaseHandlerException("Designer not added to DB: $designer");
    }
    $logger->logString("Added $designer");
    $logger->logToTelegram("Added $designer\n\n#designer");
} catch (Error|Exception $e) {
    if ($e->getMessage() === 'Filter empty queries') {
        exit();
    }
    $logger = new Logger();
    $logger->logError($e);
}

