<?php

namespace src;

use PHPUnit\Framework\TestCase;
use src\Exceptions\DesignerException;

class DesignerTest extends TestCase
{
    /**
     * @dataProvider positiveTests
     */
    public function testCreatingDesignerInstance($input, $expected): void
    {
        $designer = new Designer($input);
        $actual = [$designer->getName(), $designer->getPhone()];
        $this->assertSame($expected, $actual);
    }

    public function positiveTests(): array
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
    public function testTrowingException($input): void
    {
        $this->expectException(DesignerException::class);
        new Designer($input);
    }

    public function negativeTests(): array
    {
        return [
            ['Юлия_Ситникова_+79123d456780'],
            ['8(912)345-67-8_Юлия_Ситникова_'],
            ['Юлия_Ситникова_912 35-67 80'],
        ];
    }
}
