<?php

use src\DatabaseHandler;
use src\DatabaseConfig;

include_once ('vendor/autoload.php');

if ($_GET['action'] === 'delete_tests') {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
    $config = new DatabaseConfig();
    $pdo = new PDO($config->getDsn(), $config->getUser(), $config->getPassword());
    $dbHandler = new DatabaseHandler($pdo);
    $dbHandler->deleteTests();
}

if ($_GET['action'] === 'add') {
    $get = [
        'docnumber'  => 'A000000-0',
        'totalSum' => '321',
        'prepayment' => '123',
        'designer' => 'Якушова_Юлия_9032233169',
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

header('Location: http://t91265r5.beget.tech/base.php');
