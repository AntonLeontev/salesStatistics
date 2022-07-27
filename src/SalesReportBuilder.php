<?php

namespace src;

use DateTime;
use PDO;

class SalesReportBuilder
{
    private PDO $pdo;
    private DateTime $date;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->date = new DateTime();
    }

    public function build(): SalesReport
    {
        $data = $this->getData();
        return new SalesReport($data);
    }

    private function getData()
    {
        $todaySales = $this->getTodaySales($this->date);
        $todayPaidOrders = $this->getTodayPaidOrders($this->date);
        $minSalesPerMonth = $this->getMinSalesPerMonth($this->date);
        $maxSalesPerMonth = $this->getMaxSalesPerMonth($this->date);
        $ordersNumberPerMonth = $this->getOrdersNumber($this->date);
        $paidOrders = $this->getPrepaidOrdersNumber($this->date);
        $paidThisMonthOrders = $this->getPrepaidOrdersNumberThisMonth($this->date);
        $averageCheckPerMonth = $minSalesPerMonth / $paidOrders;
        $averageCheckTotal = $this->getTotalAverageCheck();

        return compact(
            'todaySales',
            'todayPaidOrders',
            'minSalesPerMonth',
            'maxSalesPerMonth',
            'ordersNumberPerMonth',
            'paidOrders',
            'paidThisMonthOrders',
            'averageCheckPerMonth',
            'averageCheckTotal'
        );
    }

    private function getTodaySales(DateTime $date): int
    {
        $sql = 'SELECT SUM(`total_sum`) FROM `orders` 
                WHERE prepaid_at >= :today AND prepaid_at <= LAST_DAY(:today)';
        $today = $date->format('Y-m-d');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(compact('today'));
        $result = $stmt->fetch();
        return $result[0];
    }

    private function getTodayPaidOrders(DateTime $date): int
    {
        $sql = 'SELECT COUNT(`total_sum`) FROM `orders` 
                WHERE prepaid_at >= :today AND prepaid_at <= LAST_DAY(:today)';
        $today = $date->format('Y-m-d');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(compact('today'));
        $result = $stmt->fetch();
        return $result[0];
    }

    private function getMinSalesPerMonth(DateTime $date): int
    {
        $sql = 'SELECT SUM(`total_sum`) FROM `orders` 
                WHERE prepaid_at >= :firstDay AND prepaid_at <= LAST_DAY(:firstDay)';

        $result = $this->getPerMonth($date, $sql);
        return $result[0] ?? 0;
    }

    private function getMaxSalesPerMonth(DateTime $date): int
    {
        $sql = 'SELECT SUM(`total_sum`) FROM `orders` 
                WHERE updated_at >= :firstDay AND updated_at <= LAST_DAY(:firstDay)
                AND address <> "" AND free_drive <> ""';

        $result = $this->getPerMonth($date, $sql);
        return $result[0] ?? 0;
    }

    private function getOrdersNumber(DateTime $date): int
    {
        $sql = 'SELECT COUNT(*) FROM `orders`
                WHERE updated_at >= :firstDay AND updated_at <= LAST_DAY(:firstDay)';
        $result = $this->getPerMonth($date, $sql);
        return $result[0];
    }

    private function getPrepaidOrdersNumber(DateTime $date): int
    {
        $sql = 'SELECT COUNT(*) FROM `orders`
                WHERE prepaid_at >= :firstDay AND prepaid_at <= LAST_DAY(:firstDay)';
        $result = $this->getPerMonth($date, $sql);
        return $result[0];
    }

    private function getPrepaidOrdersNumberThisMonth(DateTime $date): int
    {
        $sql = 'SELECT COUNT(*) FROM `orders`
                WHERE prepaid_at >= :firstDay AND prepaid_at <= LAST_DAY(:firstDay) 
                AND created_at >= :firstDay AND created_at <= LAST_DAY(:firstDay)';
        $result = $this->getPerMonth($date, $sql);
        return $result[0];
    }

    private function getPerMonth(DateTime $date, string $sql)
    {
        $firstDay = $date->format('Y-m-01');
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(compact('firstDay'));
        return $stmt->fetch();
    }

    private function getTotalAverageCheck(): int
    {
        $sql = 'SELECT AVG(`total_sum`) FROM `orders` WHERE prepayment > 0';
        $stmt = $this->pdo->query($sql);
        $result = $stmt->fetch();
        return $result[0];
    }
}
