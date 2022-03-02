<?php

namespace src;

use Error;
use Exception;

class Logger
{
    public function logError(Error|Exception $e)
    {
        $message = sprintf(
            '%s | %s | Line %s',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        $this->logString($message);
    }

    public function logString(string $string = 'Success')
    {
        $time = new \DateTime('now');
        $message = sprintf(
            '%s | %s' . PHP_EOL,
            $time->format('d M Y H:i:s'),
            $string
        );
        file_put_contents(__DIR__ . '/../errors.log', $message, FILE_APPEND);
    }

    public function logToTelegram(string $text)
    {
        $get = [
            'chat_id'  => $_ENV['TG_CHAT_ID'],
            'parse_mode' => 'html',
            'text' => $text,
        ];
        $url = sprintf(
            'https://api.telegram.org/bot%s/sendMessage?%s',
            $_ENV['TG_TOKEN'],
            http_build_query($get)
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $html = curl_exec($ch);
        curl_close($ch);
    }
}