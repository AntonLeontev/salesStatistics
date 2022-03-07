<?php

namespace src;

use PHPUnit\Framework\TestCase;

class QueryHandlerTest extends TestCase
{
    /**
     * @dataProvider queryHandlingTests
     */
    public function testQueryHandling($input, $expected): void
    {
        $queryHandler = new QueryHandler();
        $result = $queryHandler->handleQuery($input);
        $this->assertSame($expected, $result);
    }

    public function queryHandlingTests(): array
    {
        return [
            [['key1'=>'1', 'key2'=>'2'], 'key1=1&key2=2'],
            [['key1=1&key2=2?key1=1&key2=2'=>''], 'key1=1&key2=2'],
        ];
    }

    /**
     * @dataProvider queryParsingTests
     */
    public function testQueryParsing($input, $expected): void
    {
        $queryHandler = new QueryHandler();
        $result = $queryHandler->parseQuery($input);
        $this->assertSame($expected, $result);
    }

    public function queryParsingTests(): array
    {
        return [
            ['key1=1&key2=2', ['key1'=>'1', 'key2'=>'2']],
        ];
    }
}
