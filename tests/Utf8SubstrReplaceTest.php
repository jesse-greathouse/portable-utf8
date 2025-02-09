<?php

declare(strict_types=1);

namespace jessegreathouse\tests;

use jessegreathouse\helper\UTF8 as u;

/**
 * Class Utf8SubstrReplaceTest
 *
 * @internal
 */
final class Utf8SubstrReplaceTest extends \PHPUnit\Framework\TestCase
{
    public function testReplaceStart()
    {
        $str = 'Iñtërnâtiônàlizætiøn';
        $replaced = 'IñtërnâtX';
        static::assertSame($replaced, u::substrReplace($str, 'X', 8));
    }

    public function testEmptyString()
    {
        $str = '';
        $replaced = 'X';
        static::assertSame($replaced, u::substrReplace($str, 'X', 8));
    }

    public function testNegative()
    {
        for ($i = 0; $i < 2; ++$i) { // keep this loop for simple performance tests
            $str = 'testing';
            $replaced = \substr_replace($str, 'foo', 0, -2);
            static::assertSame($replaced, u::substrReplace($str, 'foo', 0, -2));

            $str = 'testing';
            $replaced = \substr_replace($str, 'foo', -2, 0);
            static::assertSame($replaced, u::substrReplace($str, 'foo', -2, 0));

            $str = 'testing';
            $replaced = \substr_replace($str, 'foo', -2, -2);
            static::assertSame($replaced, u::substrReplace($str, 'foo', -2, -2));

            $str = ['testing'];
            $replaced = \substr_replace($str, 'foo', -2, -2);
            static::assertSame($replaced, u::substrReplace($str, 'foo', -2, -2));

            $str = 'testing';
            $replaced = \substr_replace($str, ['foo'], -2, -2);
            static::assertSame($replaced, u::substrReplace($str, ['foo'], -2, -2));

            $str = 'testing';
            $replaced = \substr_replace($str, [], -2, -2);
            static::assertSame($replaced, u::substrReplace($str, [], -2, -2));

            $str = ['testing', 'testingV2'];
            $replaced = \substr_replace($str, ['foo', 'fooV2'], -2, -2);
            static::assertSame($replaced, u::substrReplace($str, ['foo', 'fooV2'], -2, -2));

            $str = ['testing', 'testingV2'];
            $replaced = \substr_replace($str, ['foo', 'fooV2'], [1, 2], [-1, 1]);
            static::assertSame($replaced, u::substrReplace($str, ['foo', 'fooV2'], [1, 2], [-1, 1]));

            $str = ['testing', 'testingV2'];
            $replaced = \substr_replace($str, ['foo', 'fooV2'], -2, [-1, 1]);
            static::assertSame($replaced, u::substrReplace($str, ['foo', 'fooV2'], -2, [-1, 1]));

            $str = ['testing', 'testingV2'];
            $replaced = \substr_replace($str, ['foo', 'fooV2'], [1, 2], -1);
            static::assertSame($replaced, u::substrReplace($str, ['foo', 'fooV2'], [1, 2], -1));

            $str = 'testing';
            $replaced = \substr_replace($str, [], -2, -2);
            static::assertSame($replaced, u::substrReplace($str, [], -2, -2));

            $str = ['testing', 'lall'];
            $replaced = \substr_replace($str, 'foo', -2, -2);
            static::assertSame($replaced, u::substrReplace($str, 'foo', -2, -2));

            $str = ['foo', 'lall'];
            $replaced = \substr_replace($str, 'Iñtërnâtiônàlizætiøn', -2, -2);
            static::assertSame($replaced, u::substrReplace($str, 'Iñtërnâtiônàlizætiøn', -2, -2));

            $str = ['Iñtërnâtiônàlizætiøn', 'foo'];
            //$replaced = substr_replace($str, 'foo', -2, -2); // INFO: this isn't multibyte ready
            static::assertSame(['Iñtërnâtiônàlizætifooøn', 'ffoooo'], u::substrReplace($str, 'foo', -2, -2));

            $str = ['Iñtërnâtiônàlizætiøn', 'foo'];
            //$replaced = substr_replace($str, 'æ', 1); // INFO: this isn't multibyte ready

            static::assertSame(['XIñtërnâtiônàlizætiøn', 'Xfoo'], u::substrReplace($str, 'X', 0));
            static::assertSame(['IXñtërnâtiônàlizætiøn', 'fXoo'], u::substrReplace($str, 'X', 1));
            static::assertSame(['IñtërnâtiôXnàlizætiøn', 'fooX'], u::substrReplace($str, 'X', 10));

            static::assertSame(['XIñtërnâtiônàlizætiøn', 'Xfoo'], u::substrReplace($str, 'X', [0, 0]));
            static::assertSame(['IXñtërnâtiônàlizætiøn', 'fXoo'], u::substrReplace($str, 'X', [1, 1]));
            static::assertSame(['IñtërnâtiôXnàlizætiøn', 'fooX'], u::substrReplace($str, 'X', [10, 10]));

            static::assertSame(['æIñtërnâtiônàlizætiøn', 'æfoo'], u::substrReplace($str, 'æ', 0));
            static::assertSame(['Iæñtërnâtiônàlizætiøn', 'fæoo'], u::substrReplace($str, 'æ', 1));
            static::assertSame(['Iñtërnâtiôænàlizætiøn', 'fooæ'], u::substrReplace($str, 'æ', 10));

            static::assertSame(['Iñtërnâtiôænàlizætiøn', 'fooæ'], u::substrReplace($str, 'æ', 10, 0));
            static::assertSame(['Iñtërnâtiôæàlizætiøn', 'fooæ'], u::substrReplace($str, 'æ', 10, 1));
            static::assertSame(['Iñtërnâtiôæ', 'fooæ'], u::substrReplace($str, 'æ', 10, 10));
        }
    }

    public function testZero()
    {
        $str = 'testing';
        $replaced = \substr_replace($str, 'foo', 0, 0);
        static::assertSame($replaced, u::substrReplace($str, 'foo', 0, 0));
    }

    public function testLinefeed()
    {
        $str = "Iñ\ntërnâtiônàlizætiøn";
        $replaced = "Iñ\ntërnâtX";
        static::assertSame($replaced, u::substrReplace($str, 'X', 9));

        // ---

        $str = "Iñ\ntërnâtiônàlizætiøn";
        $replaced = "Iñ\ntërnâtà";
        static::assertSame($replaced, u::substrReplace($str, 'à', 9));
    }

    public function testLinefeedReplace()
    {
        $str = "Iñ\ntërnâtiônàlizætiøn";
        $replaced = "Iñ\ntërnâtX\nY";
        static::assertSame($replaced, u::substrReplace($str, "X\nY", 9));
    }
}
