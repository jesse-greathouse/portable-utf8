<?php

declare(strict_types=0);

namespace jessegreathouse\tests;

use Symfony\Polyfill\Php72\Php72 as p;
use jessegreathouse\helper\UTF8;

/**
 * Class ShimXmlTest
 *
 * @internal
 */
final class ShimXmlTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8Encode()
    {
        $s = \array_map('chr', \range(0, 255));
        $s = \implode('', $s);
        $e = p::utf8Encode($s);

        if (UTF8::getSupportInfo('mbstring_func_overload') !== true) {
            static::assertSame(\utf8_encode($s), p::utf8Encode($s));
            static::assertSame(\utf8Decode($e), p::utf8Decode($e));

            static::assertSame('??', p::utf8Decode('Σ어'));
        }

        // ---

        $s = 444;
        static::assertSame(\utf8_encode($s), p::utf8Encode($s));
        static::assertSame(\utf8Decode($s), p::utf8Decode($s));
    }
}
