<?php

namespace src;

use src\Exceptions\DesignerException;

class DesignerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider positiveTests
     */
    public function testCreatingDesignerInstance($input, $expected)
    {
        $designer = new Designer($input);
        $actual = [$designer->getName(), $designer->getPhone()];
        $this->assertSame($expected, $actual);
    }

    public function positiveTests()
    {
        return [
            ['Юлия Ситникова +79123456780', ['Юлия Ситникова', '89123456780']],
            ['8(912)345-67-80 Юлия Ситникова ', ['Юлия Ситникова', '89123456780']],
            ['Юлия Ситникова 912 345-67 80', ['Юлия Ситникова', '89123456780']],
            ['Якушова Юлия 9032233169', ['Якушова Юлия', '89032233169']],
        ];
    }

    /**
     * @dataProvider negativeTests
     */
    public function testTrowingException($input)
    {
        $this->expectException(DesignerException::class);
        new Designer($input);
    }

    public function negativeTests()
    {
        return [
            ['Юлия_Ситникова_+79123d456780'],
            ['8(912)345-67-8_Юлия_Ситникова_'],
            ['Юлия_Ситникова_912 35-67 80'],
        ];
    }
}