<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8;
use jessegreathouse\helper\UTF8 as u;

/**
 * Class Utf8StrcspnTest
 *
 * @internal
 */
final class Utf8StrcspnTest extends \PHPUnit\Framework\TestCase
{
    public function testNoCharlist()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(20, u::strComplementSpan($str, ''));
    }

    public function testEmptyInput()
    {
        $str = '';
        static::assertSame(0, u::strComplementSpan($str, "\n"));
    }

    public function testNoMatchSingleByteSearch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(2, u::strComplementSpan($str, 't'));
    }

    public function testNoMatchSingleByteSearchAndOffset()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(6, u::strComplementSpan($str, 't', 10));
    }

    public function testNoMatchSingleByteSearchAndOffsetAndLength()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(1, u::strComplementSpan($str, 'ñ', 0, 5));

        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(5, u::strComplementSpan($str, 'ø', 1, 5));
    }

    public function testCompareStrcspn()
    {
        $str = 'aeioustr';
        static::assertSame(\strComplementSpan($str, 'tr'), u::strComplementSpan($str, 'tr'));
    }

    public function testMatchAscii()
    {
        $str = 'internationalization';
        static::assertSame(\strComplementSpan($str, 'a'), u::strComplementSpan($str, 'a'));
    }

    public function testCompatibleWithPhpNativeFunction()
    {
        $str = '';
        static::assertSame(\strComplementSpan($str, 'a'), u::strComplementSpan($str, 'a'));

        // ---

        $str = 'internationalization';
        static::assertSame(\strComplementSpan($str, ''), u::strComplementSpan($str, ''));

        // ---

        $str = 'internationalization';
        static::assertSame(\strComplementSpan($str, 't', 19), u::strComplementSpan($str, 't', 19));
    }

    public function testLinefeed()
    {
        $str = "i\nñtërnâtiônàlizætiøn";
        static::assertSame(3, u::strComplementSpan($str, 't'));
    }

    public function testLinefeedMask()
    {
        $str = "i\nñtërnâtiônàlizætiøn";
        static::assertSame(1, u::strComplementSpan($str, "\n"));
    }

    public function testNoMatchMultiByteSearch()
    {
        $str = 'iñtërnâtiônàlizætiøn';
        static::assertSame(6, u::strComplementSpan($str, 'â'));
    }

    public function testCompareStrspn()
    {
        $str = 'aeioustr';
        static::assertSame(UTF8::strComplementSpan($str, 'tr'), \strComplementSpan($str, 'tr'));
    }
}
