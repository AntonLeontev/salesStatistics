<?php

namespace src;

use PDO;

class DatabaseHandler
{
    private PDO $pdo;

    public function __construct(PDO $PDO)
    {
        $this->pdo = $PDO;
    }

    public function write(string $string): void
    {
        $sql = "INSERT INTO `rawData`(`data`) VALUES (:string)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['string'=>$string]);
    }

    public function readAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM `rawData`");
        return $stmt->fetchAll();
    }

}