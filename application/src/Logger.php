<?php

namespace src;

use Error;

class Logger
{
    public function logError(Error $e)
    {
        $time = new \DateTime('now');
        $message = sprintf(
            '%s | %s | Line %s | %s' . PHP_EOL,
            $time->format('d M Y H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        file_put_contents(__DIR__ . '/../errors.log', $message, FILE_APPEND);
    }

    public function logString($string = 'Success')
    {
        $time = new \DateTime('now');
        $message = sprintf(
            '%s %s' . PHP_EOL,
            $time->format('d M Y H:i:s'),
            $string
        );
        file_put_contents(__DIR__ . '/../errors.log', $message, FILE_APPEND);
    }
}