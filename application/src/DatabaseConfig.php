<?php

namespace src;

class DatabaseConfig
{
    private string $dsn;
    private string $user;
    private string $password;

    public function __construct()
    {
        $this->dsn = sprintf(
            '%s:dbname=%s;host=%s',
            $_ENV['DB_TYPE'],
            $_ENV['DB_NAME'],
            $_ENV['DB_HOST'],
        );
        $this->user = $_ENV['DB_USER'];
        $this->password = $_ENV['DB_PASSWORD'];
    }

    /**
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /**
     * @return mixed|string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return mixed|string
     */
    public function getPassword()
    {
        return $this->password;
    }
}
