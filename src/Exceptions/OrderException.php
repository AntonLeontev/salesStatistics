<?php

namespace src\Exceptions;

class OrderException extends \Exception
{
    public function __construct($message)
    {
        $message = 'OrderException: ' . $message;
        parent::__construct($message);
    }
}