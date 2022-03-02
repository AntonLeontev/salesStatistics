<?php

namespace src\Exceptions;

class DesignerException extends \Exception
{
    public function __construct($message)
    {
        $message = 'DesignerException: ' . $message;
        parent::__construct($message);
    }
}