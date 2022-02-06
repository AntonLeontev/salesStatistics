<?php

use src\QueryHandler;
use src\DatabaseHandler;
use Webmozart\Assert\Assert;

include_once ('vendor/autoload.php');

$pdo = new PDO('mysql:dbname=t91265r5_statist;host=127.0.0.1', 't91265r5_statist', 'Aner0102');
$dbHandler = new DatabaseHandler($pdo);
$queryHandler = new QueryHandler();

$result = $queryHandler->handleGet($_GET);

Assert::stringNotEmpty($result);
$dbHandler->write($result);

