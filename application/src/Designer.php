<?php

namespace src;

use src\Exceptions\DesignerException;

class Designer
{
    private string $name;
    private string $phone;

    /**
     * @throws DesignerException
     */
    public function __construct(string $data)
    {
        if (!preg_match('/\+?([0-9-() ]{3,})/', $data, $matches)) {
            throw new DesignerException("Can not create: $data");
        }
        $phone = $matches[1];
        $this->name = $this->prepareName(str_replace($phone, '', $data));
        $this->phone = $this->preparePhone($phone);
    }

    public function __toString()
    {
        return sprintf("%s %s", $this->name, $this->phone);
    }

    private function preparePhone(string $phone): string
    {
        $phone = str_replace(['(', ')', ' ', '-'], '', $phone);
        if (strlen($phone) === 11) {
            return '8' . substr($phone, 1);
        }
        if (strlen($phone) === 10 && str_starts_with($phone, '9')) {
            return '8' . $phone;
        }
        throw new DesignerException('Invalid phone number: ' . $phone);
    }

    private function prepareName(string $name): string
    {
        $name = str_replace(['_', '+'], [' ', ''], $name);
        return trim($name);
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }
}