<?php

namespace src;

use PHPUnit\Framework\TestCase;
use src\Exceptions\OrderException;

class OrderTest extends TestCase
{
    /**
     * @dataProvider handlingNumberPositiveTests
     */
    public function testHandlingNumber($input, $expected)
    {
        $order = new Order($input);
        $this->assertSame($expected, $order->getNumber());
    }

    public function handlingNumberPositiveTests()
    {
        return [
            [
                'docnumber=№_A-260222-4_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-4'
            ],
            [
                'docnumber=№_А260222-4_2_этап_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-4.2'
            ],
            [
                'docnumber=а-260222-12_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-12'
            ],
            [
                'docnumber=а_260222-12.2_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-12.2'
            ],
            [
                'docnumber=а260222-12/2_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-12.2'
            ],
            [
                'docnumber=а_260222-1\2_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-1.2'
            ],
            [
                'docnumber=а-260222-1-2_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-1.2'
            ],
            [
                'docnumber=а-260222-1-2_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive=',
                'A-260222-1.2'
            ],
        ];
    }

    /**
     * @dataProvider handlingNumberNegativeTests
     */
    public function testHandlingNumberThrowingException($input)
    {
        $this->expectException(OrderException::class);
        new Order($input);
    }

    public function handlingNumberNegativeTests()
    {
        return [
            ['docnumber=A-_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive='],
            ['docnumber=A-220221_от_26_02_22_г_&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive='],
            ['docnumber=&totalSum=&prepayment=&designer=&manager=&adress=&freeDrive='],
        ];
    }

    /**
     * @dataProvider handlingManagerPositiveTests
     */
    public function testHandlingManager($input, $expected)
    {
        $order = new Order($input);
        $this->assertSame($expected, $order->getManager());
    }

    public function handlingManagerPositiveTests()
    {
        return [
            [
                'docnumber=а260222-1&totalSum=&prepayment=&designer=&manager=Менеджер:_Гертман_Анна_&adress=&freeDrive=',
                'Гертман Анна'
            ],
            [
                'docnumber=а260222-1&totalSum=&prepayment=&designer=&manager=Менеджер:&adress=&freeDrive=',
                ''
            ],
            [
                'docnumber=а260222-1&totalSum=&prepayment=&designer=&manager=Гертман_Анна&adress=&freeDrive=',
                'Гертман Анна'
            ],
        ];
    }

}
