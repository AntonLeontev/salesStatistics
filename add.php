<?php

use src\DatabaseHandler;
use src\DatabaseConfig;
use src\Exceptions\DesignerException;
use src\Logger;
use src\Order;
use src\Designer;
use src\QueryHandler;

include_once('vendor/autoload.php');

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();
$config = new DatabaseConfig();
$pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
$dbHandler = new DatabaseHandler($pdo);
$queryHandler = new QueryHandler();
$logger = new Logger();

if ($_GET['action'] === 'delete_tests') {
    $dbHandler->deleteTests();
}

if ($_GET['action'] === 'add') {
    $get = [
        'docnumber'  => 'A000000-0',
        'totalSum' => '321',
        'prepayment' => '123',
        'designer' => '7_981_000-67-20_TEST',
        'manager' => 'Леонтьев Антон',
        'adress' => '',
        'freeDrive' => 'TEST',
    ];

    $ch = curl_init('http://t91265r5.beget.tech?' . http_build_query($get));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_exec($ch);
    curl_close($ch);
}

if ($_GET['action'] === 'add_orders') {
    try {
        $dbHandler->clearOrders();
        $queries = $dbHandler->readRawData();
        foreach ($queries as $query) {
            $parsedQuery = $queryHandler->parseQuery($query['data']);
            $order = new Order($parsedQuery);
            $orderId = $dbHandler->handleOrder($order);
            if ($order->getDesigner()) {
                try {
                    $designer = new Designer($order->getDesigner());
                    $designerId = $dbHandler->handleDesigner($designer);
                    $dbHandler->addDesignerInOrder($orderId, $designerId);
                } catch (DesignerException $e) {
                    $logger->logError($e);
                }
            }
        }
    } catch (Error|Exception $e) {
        $logger->logError($e);
    }
}

header('Location: http://t91265r5.beget.tech/base.php?page=orders');
