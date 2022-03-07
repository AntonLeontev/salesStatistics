<?php

namespace src;

use PHPUnit\Framework\TestCase;
use src\Exceptions\OrderException;

class OrderTest extends TestCase
{
    protected array $input;
    
    protected function setUp(): void
    {
        $this->input = [
            'docnumber' => 'A-000000-0',
            'totalSum' => '',
            'prepayment' => '',
            'designer' => '',
            'manager' => '',
            'adress' => '',
            'freeDrive' => '',
        ];
    }
    
    /**
     * @dataProvider handlingNumberPositiveTests
     */
    public function testHandlingNumber($docnumber, $expected)
    {        
        $this->input['docnumber'] = $docnumber;
        $order = new Order($this->input);
        $this->assertSame($expected, $order->getNumber());
    }

    public function handlingNumberPositiveTests()
    {
        return [
            ['№_A-260222-4_от_26_02_22_г_', 'A-260222-4'],
            ['№_А260222-4_2_этап_от_26_02_22_г_', 'A-260222-4.2'],
            ['а-260222-12_от_26_02_22_г_', 'A-260222-12'],
            ['а_260222-12.2_от_26_02_22_г_', 'A-260222-12.2'],
            ['а260222-12/2_от_26_02_22_г_', 'A-260222-12.2'],
            ['а_260222-1\2_от_26_02_22_г_', 'A-260222-1.2'],
            ['а-260222-1-2_от_26_02_22_г_', 'A-260222-1.2'],
            ['а-260222-1-2_от_26_02_22_г_', 'A-260222-1.2'],
        ];
    }

    /**
     * @dataProvider handlingNumberNegativeTests
     */
    public function testHandlingNumberThrowingException($docnumber)
    {        
        $this->input['docnumber'] = $docnumber;
        $this->expectException(OrderException::class);
        new Order($this->input);
    }

    public function handlingNumberNegativeTests()
    {
        return [
            ['A-_от_26_02_22_г_'],
            ['A-220221_от_26_02_22_г_'],
            [''],
        ];
    }

    /**
     * @dataProvider handlingManagerPositiveTests
     */
    public function testHandlingManager($manager, $expected)
    {        
        $this->input['manager'] = $manager;
        $order = new Order($this->input);
        $this->assertSame($expected, $order->getManager());
    }

    public function handlingManagerPositiveTests()
    {
        return [
            ['Менеджер:_Гертман_Анна_', 'Гертман Анна'],
            ['Менеджер:', ''],
            ['Гертман_Анна', 'Гертман Анна'],
        ];
    }
}
