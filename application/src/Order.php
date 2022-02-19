<?php

namespace src;

class Order
{
    private string $docnumber;
    private string $totalSum;
    private string $prepayment;
    private string $designer;
    private string $manager;
    private string $adres;
    private string $freeDrive;

    public function __construct(array $request)
    {
        $this->docnumber = $request['docnumber'];
        $this->totalSum = $request['totalSum'];
        $this->prepayment = $request['prepayment'];
        $this->designer = $request['designer'];
        $this->manager = $request['manager'];
        $this->adres = $request['adress'];
        $this->freeDrive = $request['freeDrive'];
    }

    public function toString(): string
    {
        return sprintf(
            'docnumber=%s&totalSum=%s&prepayment=%s&designer=%s&manager=%s&adress=%s&freeDrive=%s',
            $this->docnumber,
            $this->totalSum,
            $this->prepayment,
            $this->designer,
            $this->manager,
            $this->adres,
            $this->freeDrive
        );
    }
}