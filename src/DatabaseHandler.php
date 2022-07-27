<?php

namespace src;

use PDO;
use src\Exceptions\DatabaseHandlerException;
use Webmozart\Assert\Assert;

class DatabaseHandler
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';
    private PDO $pdo;

    public function __construct(PDO $PDO)
    {
        $this->pdo = $PDO;
    }

    /**
     * Writes given string to table raw_data
     *
     * @param string $string Data to write
     * @throws DatabaseHandlerException
     */
    public function writeRawData(string $string): void
    {
        Assert::stringNotEmpty($string, 'Filter empty queries');
        Assert::regex($string, '~[aAаА][- ]?\d{6}-\d{1,2}~', "Wrong docnumber: $string");

        $sql = 'INSERT INTO `rawData`(`data`) VALUES (?)';
        $statement = $this->pdo->prepare($sql);
        if (!$statement->execute([$string])) {
            throw new DatabaseHandlerException("Query not added to DB: $string");
        }
    }

    /**
     * Reads all data from table raw_data
     *
     * @param string $order Sorting order (ASC or DESC)
     * @return array Data from DB
     */
    public function readRawData(string $order = self::ASC): array
    {
        $sql = "SELECT `data`, `date` FROM `rawData` ORDER BY `id` $order";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getOrders(): array
    {
        $sql = 'SELECT `number`, `total_sum`, `prepayment`, `manager`, `address`, `free_drive`, `updated_at` 
            FROM `orders` ORDER BY `updated_at` DESC LIMIT 100';
        return $this->pdo->query($sql)->fetchAll();
    }

    /**
     * @throws DatabaseHandlerException
     */
    public function handleDesigner(Designer $designer): int
    {
        if ($this->designerExists($designer)) {
            return $this->getDesignerId($designer->getPhone());
        }

        $this->insertDesigner($designer);
        return $this->getDesignerId($designer->getPhone());
    }

    /**
     * Saves Order to DB and returns its ID
     *
     * @param Order $order Order instance
     * @return int Order ID
     * @throws DatabaseHandlerException
     */
    public function handleOrder(Order $order): int
    {
        if ($this->orderExists($order->getNumber())) {
            $this->updateOrder($order);
            return $this->getOrderId($order->getNumber());
        }

        $this->insertOrder($order);
        return $this->getOrderId($order->getNumber());
    }

    /**
     * Adds designer's ID into order
     *
     * @param int $orderId
     * @param int $designerId
     * @throws DatabaseHandlerException
     */
    public function addDesignerInOrder(int $orderId, int $designerId): void
    {
        $sql = 'UPDATE `orders` SET `designer_id` = ? WHERE `orders`.`id` = ?';
        $statement = $this->pdo->prepare($sql);

        if (!$statement->execute([$designerId, $orderId])) {
            throw new DatabaseHandlerException("Designer $designerId not added to order $orderId");
        }
    }

    /**
     * Removes test recordings from DB
     */
    public function deleteTests(): void
    {
        $this->pdo->query("DELETE FROM `rawData` WHERE `rawData`.`data` LIKE '%TEST%'");
        $this->pdo->query("DELETE FROM `orders` WHERE `orders`.`free_drive` = 'TEST'");
        $this->pdo->query("DELETE FROM `designers` WHERE `designers`.`name` = 'TEST'");
    }

    /**
     * Removes all recordings from table orders
     */
    public function clearOrders(): void
    {
        $this->pdo->query('DELETE FROM `orders`');
    }

    /**
     * Writes new designer into table designers
     *
     * @param Designer $designer Designer instance
     * @throws DatabaseHandlerException
     */
    private function insertDesigner(Designer $designer): void
    {
        $sql = 'INSERT INTO designers (`name`, `phone`) VALUES (?, ?)';
        $statement = $this->pdo->prepare($sql);

        if (!$statement->execute([$designer->getName(), $designer->getPhone()])) {
            throw new DatabaseHandlerException("Designer not added to DB: $designer");
        }
    }

    /**
     * Writes new order into table order
     *
     * @param Order $order Order instance
     * @throws DatabaseHandlerException
     */
    private function insertOrder(Order $order): void
    {
        $sql = 'INSERT INTO 
            orders (`number`, `total_sum`, `prepayment`, `manager`, `address`, `free_drive`) 
            VALUES (?, ?, ?, ?, ?, ?)';
        $statement = $this->pdo->prepare($sql);

        if (!$statement->execute(
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

        if ($order->getPrepayment() > 0) {
            $this->makePrepaid($order);
        }
    }

    /**
     * Updates order by number
     *
     * @param Order $order Order instance
     * @throws DatabaseHandlerException
     */
    private function updateOrder(Order $order): void
    {
        if (! $this->isPrepaid($order) && $order->getPrepayment() > 0) {
            $this->makePrepaid($order);
        }

        $sql = 'UPDATE orders
            SET `total_sum` = ?, `prepayment` = ?, `manager` = ?, `address` = ?, `free_drive` = ?, `updated_at` = NOW() 
            WHERE  `orders`.`number` = ?';
        $statement = $this->pdo->prepare($sql);

        if (!$statement->execute(
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
    }

    /**
     * Checks if given phone number exists in designers
     *
     * @param Designer $designer Designer object
     * @return bool
     */
    public function designerExists(Designer $designer): bool
    {
        $phone = $designer->getPhone();
        $sql = 'SELECT `phone` FROM `designers` WHERE `phone`=:phone LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['phone'=>$phone]);
        if ($statement->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * Finds designers id by phone number
     *
     * @param string $phone Phone number
     * @return int designer id
     */
    private function getDesignerId(string $phone): int
    {
        $sql = 'SELECT `id` FROM `designers` WHERE `phone`=:phone LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['phone'=>$phone]);
        $response = $statement->fetch();
        return $response['id'];
    }

    /**
     * Checks if order with given number exists
     *
     * @param string $number Order's number
     * @return bool
     */
    private function orderExists(string $number): bool
    {
        $sql = 'SELECT `number` FROM `orders` WHERE `number`=:number LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['number'=>$number]);
        if ($statement->fetch()) {
            return true;
        }
        return false;
    }

    /**
     * Finds order's id by number
     *
     * @param string $number Orders's number
     * @return int Order's id
     */
    private function getOrderId(string $number): int
    {
        $sql = 'SELECT `id` FROM `orders` WHERE `number`=:number LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute(['number'=>$number]);
        $response = $statement->fetch();
        return $response['id'];
    }

    private function isPrepaid(Order $order): bool
    {
        $sql = 'SELECT `prepaid_at` FROM orders WHERE  `orders`.`number` = ? AND `prepaid_at` IS NOT NULL';
        $statement = $this->pdo->prepare($sql);

        if (! $statement->execute([$order->getNumber()]) ) {
            throw new DatabaseHandlerException("Can't check prepayment: $order");
        }

        return (bool) $statement->fetch();
    }

    private function makePrepaid(Order $order): void
    {
        $sql = 'UPDATE orders
            SET `prepaid_at` = CURDATE() 
            WHERE  `orders`.`number` = ?';
        $statement = $this->pdo->prepare($sql);

        if (!$statement->execute([$order->getNumber()])) {
            throw new DatabaseHandlerException("Can't update prepaid_at: $order");
        }
    }
}
