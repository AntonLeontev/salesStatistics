<?php

namespace src;

use Error;

class Logger
{
    public function log(Error $e)
    {
        $time = new \DateTime('now');
        $message = sprintf(
            '%s | %s | Line %s | %s' . PHP_EOL,
            $e->getMessage(),
            $e->getFile(),
            $e->getLine(),
            $time->format('d M Y H:i:s')
        );
        file_put_contents(__DIR__ . '/../errors.log', $message, FILE_APPEND);
    }

    public function logString($string)
    {
        file_put_contents(__DIR__ . '/../errors.log', $string . PHP_EOL, FILE_APPEND);
    }
}