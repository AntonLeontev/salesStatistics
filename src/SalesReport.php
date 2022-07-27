<?php

namespace src;

class SalesReport
{
    public int $todaySales;
    public int $todayPaidOrders;
    public int $minSales;
    public int $maxSales;
    public int $ordersPerMonth;
    public int $paidOrders;
    public int $paidThisMonthOrders;
    public int $averageCheckPerMonth;
    public int $averageCheck;

    public function __construct(array $data)
    {
        $this->todaySales = $data['todaySales'];
        $this->todayPaidOrders = $data['todayPaidOrders'];
        $this->minSales = $data['minSalesPerMonth'];
        $this->maxSales = $data['maxSalesPerMonth'];
        $this->ordersPerMonth = $data['ordersNumberPerMonth'];
        $this->paidOrders = $data['paidOrders'];
        $this->paidThisMonthOrders = $data['paidThisMonthOrders'];
        $this->averageCheckPerMonth = $data['averageCheckPerMonth'];
        $this->averageCheck = $data['averageCheckTotal'];
    }

    public function __toString()
    {
        return sprintf(
            "Отчет за %s\n
            <b>Сегодня</b>\nПродажи %sр\nОплат %s\n
            <b>За месяц</b>\nПродажи %sр | %sр \nЗаказов %s\nОплат %s | %s\n
            <b>Средний чек</b>\nЗа месяц %sр\nЗа все время %sр",
            (new \DateTime())->format('j-F'),
            $this->format($this->todaySales),
            $this->todayPaidOrders,
            $this->format($this->minSales),
            $this->format($this->maxSales),
            $this->ordersPerMonth,
            $this->paidOrders,
            $this->paidThisMonthOrders,
            $this->format($this->averageCheckPerMonth),
            $this->format($this->averageCheck)
        );
    }

    private function format(int $number): string
    {
        return number_format($number, 0, '.', ' ');
    }
}
