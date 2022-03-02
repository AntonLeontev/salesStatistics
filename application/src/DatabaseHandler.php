<?php

namespace src;

use PDO;
use src\Exceptions\DatabaseHandlerException;

class DatabaseHandler
{
    private PDO $pdo;

    public function __construct(PDO $PDO)
    {
        $this->pdo = $PDO;
    }

    public function write(string $string): bool
    {
        $sql = "INSERT INTO `rawData`(`data`) VALUES (:string)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['string'=>$string]);
    }

    public function readAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM `rawData` ORDER BY `id` DESC");
        return $stmt->fetchAll();
    }

    /**
     * @throws DatabaseHandlerException
     */
    public function addDesigner(Designer $designer): bool
    {
        if ($this->designerExists($designer->getPhone())) {
            throw new DatabaseHandlerException('Designer already exists ' . $designer);
        }

        $sql = "INSERT INTO designers (`name`, `phone`) VALUES (:name, :phone)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['name'=>$designer->getName(), 'phone'=>$designer->getPhone()]);
    }

    public function deleteTests()
    {
        $this->pdo->query("DELETE FROM `rawData` WHERE `rawData`.`data` LIKE '%TEST%'");
    }

    private function designerExists($value): bool
    {
        $sql = "SELECT `phone` FROM `designers` WHERE `phone`=:value LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['value'=>$value]);
        if ($stmt->fetch()) {
            return true;
        }
        return false;
    }
}
