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
    public function getDsn(): string
    {
        return $this->dsn;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
