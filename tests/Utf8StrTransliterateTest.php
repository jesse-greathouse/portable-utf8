<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8 as u;

/**
 * Class Utf8StrTransliterateTest
 *
 * @internal
 */
final class Utf8StrTransliterateTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'testiñg';
        static::assertSame('testing', u::toAscii($str));

        $str = '  -ABC-中文空白-  ';
        $expected = '  -ABC-Zhong Wen Kong Bai -  ';
        static::assertSame($expected, u::toAscii($str));
    }

    public function testAscii()
    {
        $str = 'testing';
        static::assertSame('testing', u::toAscii($str));
    }

    public function testInvalidChar()
    {
        $str = "tes\xE9ting";
        static::assertSame('testing', u::toAscii($str));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertEmpty(u::toAscii($str));
    }

    public function testNulAndNon7Bit()
    {
        $str = "a\x00ñ\x00c";
        static::assertSame('anc', u::toAscii($str));
    }

    public function testNul()
    {
        $str = "a\x00b\x00c";
        static::assertSame('abc', u::toAscii($str));
    }
}
