<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8;
use jessegreathouse\helper\UTF8 as u;

/**
 * Class Utf8StrIreplaceTest
 *
 * @internal
 */
final class Utf8StrIreplaceTest extends \PHPUnit\Framework\TestCase
{
    public function testReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::strReplaceInsensitive('lIzÆ', 'lise', $str));

        $str = ['Iñtërnâtiônàlizætiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
        $replaced = ['Iñtërnâtiônàlisetiøn', 'Iñtërnâtiônàlisetiøn', 'foobar', '', "\0", ' '];
        static::assertSame($replaced, u::strReplaceInsensitive('lIzÆ', 'lise', $str));

        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, UTF8::strReplaceInsensitive('lIzÆ', 'lise', $str));
    }

    public function testReplaceNoMatch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::strReplaceInsensitive('foo', 'bar', $str));
    }

    public function testEmptyString()
    {
        $str = '';
        $replaced = '';
        static::assertSame($replaced, u::strReplaceInsensitive('foo', 'bar', $str));
    }

    public function testEmptySearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::strReplaceInsensitive('', 'x', $str));

        // --

        static::assertSame('Iñtërnâtiônàlizætiøn', u::strReplaceInsensitive('', null, $str));
        static::assertSame('Iñtërnâtiônàlizætiøn', u::strReplaceInsensitive([], null, $str));
        static::assertSame('Iñtërnâtiônàlizætiøn', u::strReplaceInsensitive(null, null, $str));
        static::assertSame('', u::strReplaceInsensitive(null, null, null));
    }

    public function testReplaceCount()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXâtiôXàlizætiøX';
        static::assertSame($replaced, u::strReplaceInsensitive('n', 'X', $str, $count));
        static::assertSame(3, $count);
    }

    public function testReplaceDifferentSearchReplaceLength()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
        static::assertSame($replaced, u::strReplaceInsensitive('n', 'XXX', $str));
    }

    public function testReplaceArrayAsciiSearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñyërxâyiôxàlizæyiøx';
        static::assertSame(
            $replaced,
            u::strReplaceInsensitive(
                [
                    'n',
                    't',
                ],
                [
                    'x',
                    'y',
                ],
                $str
            )
        );
    }

    public function testReplaceArrayUtf8Search()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâti??nàliz????ti???n';
        static::assertSame(
            u::strReplaceInsensitive(
                [
                    'Ñ',
                    'ô',
                    'ø',
                    'Æ',
                ],
                [
                    '?',
                    '??',
                    '???',
                    '????',
                ],
                $str
            ),
            $replaced
        );
    }

    public function testReplaceArrayStringReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâti?nàliz?ti?n';
        static::assertSame(
            $replaced,
            u::strReplaceInsensitive(
                [
                    'Ñ',
                    'ô',
                    'ø',
                    'Æ',
                ],
                '?',
                $str
            )
        );
    }

    public function testReplaceArraySingleArrayReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'I?tërnâtinàliztin';
        static::assertSame(
            u::strReplaceInsensitive(
                [
                    'Ñ',
                    'ô',
                    'ø',
                    'Æ',
                ],
                ['?'],
                $str
            ),
            $replaced
        );
    }

    public function testReplaceLinefeed()
    {
        $str = "Iñtërnâti\nônàlizætiøn";
        $replaced = "Iñtërnâti\nônàlisetiøn";
        static::assertSame($replaced, u::strReplaceInsensitive('lIzÆ', 'lise', $str));
    }

    public function testReplaceLinefeedArray()
    {
        $str = "Iñtërnâti\nônàlizætiøn";
        $replaced = "Iñtërnâti\n\nônàlisetiøn";
        static::assertSame($replaced, u::strReplaceInsensitive(['lIzÆ', "\n"], ['lise', "\n\n"], $str));
    }

    public function testReplaceLinefeedSearch()
    {
        $str = "Iñtërnâtiônàli\nzætiøn";
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::strReplaceInsensitive("lI\nzÆ", 'lise', $str));
    }
}
