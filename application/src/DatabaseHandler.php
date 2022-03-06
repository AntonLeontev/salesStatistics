<?php

namespace src;

use PDO;
use src\Exceptions\DatabaseHandlerException;
use Webmozart\Assert\Assert;

class DatabaseHandler
{
    const ASC = 'ASC';
    const DESC = 'DESC';
    private PDO $pdo;

    public function __construct(PDO $PDO)
    {
        $this->pdo = $PDO;
    }

    /**
     * @throws DatabaseHandlerException
     */
    public function write(string $string): void
    {
        Assert::stringNotEmpty($string, 'Filter empty queries');
        Assert::regex($string, '~[aAаА][- ]?\d{6}-\d{1,2}~', "Wrong docnumber: $string");

        $sql = "INSERT INTO `rawData`(`data`) VALUES (:string)";
        $stmt = $this->pdo->prepare($sql);
        if (!$stmt->execute(['string'=>$string])) {
            throw new DatabaseHandlerException("Query not added to DB: $string");
        }
    }

    public function readRawData(string $order = self::ASC): array
    {
        $stmt = $this->pdo->query("SELECT * FROM `rawData` ORDER BY `id` $order");
        return $stmt->fetchAll();
    }

    /**
     * @throws DatabaseHandlerException
     */
    public function addDesigner(Designer $designer): int
    {
        if ($this->designerExists($designer->getPhone())) {
            return $this->getDesignerId($designer->getPhone());
        }

        $sql = "INSERT INTO designers (`name`, `phone`) VALUES (:name, :phone)";
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute(['name'=>$designer->getName(), 'phone'=>$designer->getPhone()])) {
            throw new DatabaseHandlerException("Designer not added to DB: $designer");
        }
        return $this->getDesignerId($designer->getPhone());
    }

    public function addOrder(Order $order): int
    {
        if ($this->orderExists($order->getNumber())) {
            $sql = "UPDATE orders
            SET `total_sum` = ?, `prepayment` = ?, `manager` = ?, `address` = ?, `free_drive` = ?, `updated_at` = NOW() 
            WHERE  `orders`.`number` = ?";
            $stmt = $this->pdo->prepare($sql);

            if (!$stmt->execute(
                [
                    $order->getTotalSum(),
                    $order->getPrepayment(),
                    $order->getManager(),
                    $order->getAddress(),
                    $order->getFreeDrive(),
                    $order->getNumber(),
                ]
            )) {
                throw new DatabaseHandlerException("Order not added to DB: $order");
            }
            return $this->getOrderId($order->getNumber());
        }

        $sql = "INSERT INTO 
            orders (`number`, `total_sum`, `prepayment`, `manager`, `address`, `free_drive`) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute(
            [
                $order->getNumber(),
                $order->getTotalSum(),
                $order->getPrepayment(),
                $order->getManager(),
                $order->getAddress(),
                $order->getFreeDrive()
            ]
        )) {
            throw new DatabaseHandlerException("Order not added to DB: $order");
        }
        return $this->getOrderId($order->getNumber());
    }

    public function addDesignerInOrder(int $orderId, int $designerId)
    {
        $sql = "UPDATE `orders` SET `designer_id` = ? WHERE `orders`.`id` = ?";
        $stmt = $this->pdo->prepare($sql);

        if (!$stmt->execute([$designerId, $orderId])) {
            throw new DatabaseHandlerException("Designer $designerId not added to order $orderId");
        }
    }

    public function deleteTests()
    {
        $this->pdo->query("DELETE FROM `rawData` WHERE `rawData`.`data` LIKE '%TEST%'");
        $this->pdo->query("DELETE FROM `orders` WHERE `orders`.`free_drive` = 'TEST'");
        $this->pdo->query("DELETE FROM `designers` WHERE `designers`.`name` = 'TEST'");
    }

    private function designerExists(string $phone): bool
    {
        $sql = "SELECT `phone` FROM `designers` WHERE `phone`=:phone LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['phone'=>$phone]);
        if ($stmt->fetch()) {
            return true;
        }
        return false;
    }

    private function getDesignerId(string $phone): int
    {
        $sql = "SELECT `id` FROM `designers` WHERE `phone`=:phone LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['phone'=>$phone]);
        $response = $stmt->fetch();
        return $response['id'];
    }

    private function orderExists(string $number): bool
    {
        $sql = "SELECT `number` FROM `orders` WHERE `number`=:number LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['number'=>$number]);
        if ($stmt->fetch()) {
            return true;
        }
        return false;
    }

    private function getOrderId(string $number): int
    {
        $sql = "SELECT `id` FROM `orders` WHERE `number`=:number LIMIT 1";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['number'=>$number]);
        $response = $stmt->fetch();
        return $response['id'];
    }

    public function clearOrders()
    {
        $this->pdo->query("DELETE FROM `orders`");
    }
}
