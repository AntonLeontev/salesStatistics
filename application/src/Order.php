<?php

namespace src;

use src\Exceptions\OrderException;

class Order
{
    private string $number;
    private int $totalSum;
    private int $prepayment;
    private string $designer;
    private string $manager;
    private string $address;
    private string $freeDrive;

    /**
     * @throws OrderException
     */
    public function __construct(string $data)
    {
        parse_str($data, $parsedData);
        $this->number = $this->handleNumber($parsedData['docnumber']);
        $this->totalSum = (int) $parsedData['totalSum'];
        $this->prepayment = (int) $parsedData['prepayment'];
        $this->designer = $this->replaceUnderlines($parsedData['designer']);
        $this->manager = $this->handleManager($parsedData['manager']);
        $this->address = $this->replaceUnderlines($parsedData['adress']);
        $this->freeDrive = $this->replaceUnderlines($parsedData['freeDrive']);
    }

    /**
     * @throws OrderException
     */
    private function handleNumber(string $data): string
    {
        $pattern = "/[aAаА][\-_]?([0-9]{6})[\-_](\d{1,2})[^a-zA-Z0-9]?(\d)?/";
        if (!preg_match($pattern, $data, $matches)) {
            throw new OrderException('Wrong number: ' . $data);
        }
        $result = sprintf('A-%s-%s', $matches[1], $matches[2]);

        if (isset($matches[3])) {
            return $result . '.' . $matches[3];
        }
        return $result;
    }

    private function handleManager(string $data): string
    {
        if (!$data) {
            return '';
        }

        if (str_starts_with($data, 'Менеджер')) {
            preg_match('/Менеджер:(.*)/', $data, $manager);
            return $this->replaceUnderlines($manager[1]);
        }

        return $this->replaceUnderlines($data);
    }

    private function replaceUnderlines(string $data): string
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
    public function getDesigner(): string
    {
        return $this->designer;
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
}