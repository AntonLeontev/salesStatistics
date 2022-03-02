<?php

namespace src\Exceptions;

class DatabaseHandlerException extends \Exception
{
    public function __construct($message)
    {
        $message = 'DatabaseHandler: ' . $message;
        parent::__construct($message);
    }
}