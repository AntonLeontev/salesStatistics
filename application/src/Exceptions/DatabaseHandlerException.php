<?php

namespace src\Exceptions;

class DatabaseHandlerException extends \Exception
{
    public function __construct($message, $code = 0)
    {
        $message = "DatabaseHandler: $message";
        parent::__construct($message, $code);
    }
}