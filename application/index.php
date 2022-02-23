<?php

use src\DatabaseConfig;
use src\Logger;
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

    $result = $queryHandler->handleGet($_GET);

    Assert::stringNotEmpty($result);
    if (preg_match('~[aAаА][- ]?\d{6}-\d{1,2}~', $result)) {
        $dbHandler->write($result);
        $logger->logString();
        $logger->logToTelegram($result . "\n\n#success");
        return;
    }
    $logger->logString($result);
    $logger->logToTelegram($result . "\n\n#wrongData");
} catch (Error $e) {
    $logger = new Logger();
    $logger->logError($e);
}

