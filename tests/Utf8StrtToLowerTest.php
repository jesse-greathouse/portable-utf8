<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8;

/**
 * Class Utf8StrtToLowerTest
 *
 * @internal
 */
final class Utf8StrtToLowerTest extends \PHPUnit\Framework\TestCase
{
    public function testLower()
    {
        $str = 'IÑTËRNÂTIÔNÀLIZÆTIØN';
        $lower = 'iñtërnâtiônàlizætiøn';
        static::assertSame(UTF8::strtolower($str), $lower);
    }

    public function testEmptyString()
    {
        $str = '';
        $lower = '';
        static::assertSame(UTF8::strtolower($str), $lower);
    }
}
