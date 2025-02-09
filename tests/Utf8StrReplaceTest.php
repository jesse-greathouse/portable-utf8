<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8 as u;

/**
 * Class Utf8StrReplaceTest
 *
 * @internal
 */
final class Utf8StrReplaceTest extends \PHPUnit\Framework\TestCase
{
    public function testReplace()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::strReplace('lizæ', 'lise', $str));

        $str = 'Привет мир';
        $replaced = 'Пока мир';
        static::assertSame($replaced, u::strReplace('Привет', 'Пока', $str));
    }

    public function testReplaceNoMatch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::strReplace('foo', 'bar', $str));
    }

    public function testEmptyString()
    {
        $str = '';
        $replaced = '';
        static::assertSame($replaced, u::strReplace('foo', 'bar', $str));
    }

    public function testEmptySearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñtërnâtiônàlizætiøn';
        static::assertSame($replaced, u::strReplace('', 'x', $str));
    }

    public function testReplaceCount()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXâtiôXàlizætiøX';
        static::assertSame($replaced, u::strReplace('n', 'X', $str, $count));
        static::assertSame(3, $count);
    }

    public function testReplaceDifferentSearchReplaceLength()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërXXXâtiôXXXàlizætiøXXX';
        static::assertSame($replaced, u::strReplace('n', 'XXX', $str));
    }

    public function testReplaceArrayAsciiSearch()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'Iñyërxâyiôxàlizæyiøx';
        static::assertSame(
            $replaced,
            u::strReplace(
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
            u::strReplace(
                [
                    'ñ',
                    'ô',
                    'ø',
                    'æ',
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
            u::strReplace(
                [
                    'ñ',
                    'ô',
                    'ø',
                    'æ',
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
            u::strReplace(
                [
                    'ñ',
                    'ô',
                    'ø',
                    'æ',
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
        static::assertSame($replaced, u::strReplace('lizæ', 'lise', $str));
    }

    public function testReplaceLinefeedSearch()
    {
        $str = "Iñtërnâtiônàli\nzætiøn";
        $replaced = 'Iñtërnâtiônàlisetiøn';
        static::assertSame($replaced, u::strReplace("li\nzæ", 'lise', $str));
    }
}
