<?php

namespace src;

class DatabaseConfig
{
    private string $dsn;
    private string $user;
    private string $password;

    public function __construct(string $path)
    {
        parse_str(rtrim(file_get_contents($path)), $config);
        $this->dsn = $config['dsn'];
        $this->user = $config['user'];
        $this->password = $config['password'];
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
