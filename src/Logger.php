<?php

namespace src;

use DateTime;
use Error;
use Exception;

class Logger
{
    /**
     * Formats exception or error and writes information
     * to logfile
     *
     * @param Error|Exception $e Instance to handle
     */
    public function logError(Error|Exception $e): void
    {
        $time = new DateTime('now');
        $message = sprintf(
            '%s | %s | %s | Line %s',
            $time->format('d M Y H:i:s'),
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        );
        $this->log($message);
    }

    /**
     * Formats string and writes it to logfile
     *
     * @param string $string String to handle
     */
    public function logString(string $string): void
    {
        $time = new DateTime('now');
        $message = sprintf(
            '%s | %s' . PHP_EOL,
            $time->format('d M Y H:i:s'),
            $string
        );
        $this->log($message);
    }

    /**
     * Writes text to logfile
     *
     * @param string $string String to write
     */
    private function log(string $string): void
    {
        file_put_contents(__DIR__ . '/application' . $_ENV['LOG_FILE_PATH'], $string, FILE_APPEND);
    }

    /**
     * Sends message to Telegram
     *
     * @param string $text Message text
     */
    public function logToTelegram(string $text): void
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
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_exec($ch);
        curl_close($ch);
    }
}
