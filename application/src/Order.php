<?php

namespace src;

use src\Exceptions\OrderException;

class Order
{
    private string $number;
    private int $totalSum;
    private int $prepayment;
    private string $manager;
    private string $designer;
    private string $address;
    private string $freeDrive;

    /**
     * @throws OrderException
     */
    public function __construct(array $orderData)
    {
        $this->number = $this->handleNumber($orderData['docnumber']);
        $this->totalSum = (int) $orderData['totalSum'];
        $this->prepayment = (int) $orderData['prepayment'];
        $this->manager = $this->handleManager($orderData['manager']);
        $this->designer = $this->replaceUnderlines($orderData['designer']);
        $this->address = $this->replaceUnderlines($orderData['adress']);
        $this->freeDrive = $this->replaceUnderlines($orderData['freeDrive']);
    }

    /**
     * @throws OrderException
     */
    private function handleNumber(string $data): string
    {
        $pattern = "/[aAаА][\-_]?([0-9]{6})[\-_](\d{1,2})[^a-zA-Z0-9]?(\d)?/";
        if (!preg_match($pattern, $data, $matches)) {
            throw new OrderException("Wrong number: $data");
        }
        $result = sprintf('A-%s-%s', $matches[1], $matches[2]);

        if (isset($matches[3])) {
            return sprintf('%s.%s', $result, $matches[3]);
        }
        return $result;
    }

    private function handleManager(string $data): string
    {
        if (!$data) {
            return '';
        }

        if (preg_match('/Менеджер:(.*)/', $data, $manager)) {
            return $this->replaceUnderlines($manager[1]);
        }

        return $this->replaceUnderlines($data);
    }

    protected function replaceUnderlines(string $data): string
    {
        if (!$data) {
            return '';
        }
        return trim(str_replace('_', ' ', $data));
    }

    /**
     * @return string
     */
    public function getNumber(): string
    {
        return $this->number;
    }

    /**
     * @return int
     */
    public function getTotalSum(): int
    {
        return $this->totalSum;
    }

    /**
     * @return int
     */
    public function getPrepayment(): int
    {
        return $this->prepayment;
    }

    /**
     * @return string
     */
    public function getManager(): string
    {
        return $this->manager;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return string
     */
    public function getFreeDrive(): string
    {
        return $this->freeDrive;
    }

    public function __toString()
    {
        return $this->getNumber();
    }

    /**
     * @return string
     */
    public function getDesigner(): string
    {
        return $this->designer;
    }
}