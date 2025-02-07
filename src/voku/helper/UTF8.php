<?php

declare(strict_types=1);

namespace jessegreathouse\helper;

use \Error,
    \finfo,
    \IntlChar,
    \InvalidArgumentException,
    \Normalizer,
    \RuntimeException;

final class UTF8
{
    const UTF8 = 'UTF-8';
    const UTF8_ALT = 'UTF8';
    const ASCII = 'ASCII';
    const BASE64 = 'BASE64';
    const CP850 = 'CP850';
    const CP932 = 'CP932';
    const CP936 = 'CP936';
    const CP950 = 'CP950';
    const CP866 = 'CP866';
    const CP51932 = 'CP51932';
    const CP50220 = 'CP50220';
    const CP50221 = 'CP50221';
    const CP50222 = 'CP50222';
    const EUC_CN = 'EUC-CN';
    const EUC_JP = 'EUC-JP';
    const HTML = 'HTML';
    const HTML_ENTITIES = 'HTML-ENTITIES';
    const ISO2022JP = 'ISO-2022-JP';
    const ISO2022KR = 'ISO-2022-KR';
    const ISO88591 = 'ISO-8859-1';
    const ISO88592 = 'ISO-8859-2';
    const ISO88593 = 'ISO-8859-3';
    const ISO88594 = 'ISO-8859-4';
    const ISO88595 = 'ISO-8859-5';
    const ISO88596 = 'ISO-8859-6';
    const ISO88597 = 'ISO-8859-7';
    const ISO88598 = 'ISO-8859-8';
    const ISO88599 = 'ISO-8859-9';
    const ISO885910 = 'ISO-8859-10';
    const ISO885913 = 'ISO-8859-13';
    const ISO885914 = 'ISO-8859-14';
    const ISO885915 = 'ISO-8859-15';
    const ISO885916 = 'ISO-8859-16';
    const JIS = 'JIS';
    const JIS_MS = 'JIS-ms';
    const JSON = 'JSON';
    const UTF32LE = 'UTF-32LE';
    const UTF32BE = 'UTF-32BE';
    const UTF16LE = 'UTF-16LE';
    const UTF16BE = 'UTF-16BE';
    const WINDOWS1252 = 'WINDOWS-1252';
    const WINDOWS1251 = 'WINDOWS-1251';
    const WINDOWS1254 = 'WINDOWS-1254';

    const FEATURE_TYPE_EXTENSION = 'extension';
    const FEATURE_TYPE_FUNCTION = 'function';
    const FEATURE_TYPE_CLASS = 'class';

    const FEATURE_MBSTRING = 'mbstring';
    const FEATURE_JSON = 'json';
    const FEATURE_INTL = 'intl';
    const FEATURE_INTLCHAR = 'intlChar';
    const FEATURE_ICONV = 'iconv';
    const FEATURE_CTYPE = 'ctype';
    const FEATURE_FINFO = 'finfo';
    const FEATURE_PCREUTF8 = 'pcreUtf8';
    const FEATURE_SYMFONY_POLYFILL = 'symfony_polyfill';
    const FEATURE_MBSTRING_INTERNAL = 'mbstring_internal_encoding';
    const FEATURE_MBSTRING_OVERLOAD = 'mbstring_func_overload';

    const FEATURE_MAP = [
        self::FEATURE_MBSTRING  => self::FEATURE_TYPE_EXTENSION, 
        self::FEATURE_JSON      => self::FEATURE_TYPE_FUNCTION, 
        self::FEATURE_INTL      => self::FEATURE_TYPE_EXTENSION, 
        self::FEATURE_INTLCHAR  => self::FEATURE_TYPE_CLASS, 
        self::FEATURE_ICONV     => self::FEATURE_TYPE_EXTENSION, 
        self::FEATURE_CTYPE     => self::FEATURE_TYPE_EXTENSION, 
        self::FEATURE_FINFO     => self::FEATURE_TYPE_CLASS,
    ];

    private const ENCODING_ORDER = [
        self::ISO88591,
        self::ISO88592,
        self::ISO88593,
        self::ISO88594,
        self::ISO88595,
        self::ISO88596,
        self::ISO88597,
        self::ISO88598,
        self::ISO88599,
        self::ISO885910,
        self::ISO885913,
        self::ISO885914,
        self::ISO885915,
        self::ISO885916,
        self::WINDOWS1251,
        self::WINDOWS1254,
        self::CP932,
        self::CP936,
        self::CP950,
        self::CP866,
        self::CP51932,
        self::CP50220,
        self::CP50221,
        self::CP50222,
        self::ISO2022JP,
        self::ISO2022KR,
        self::JIS,
        self::JIS_MS,
        self::EUC_CN,
        self::EUC_JP,
    ];

    /**
     * Bom => Byte-Length
     *
     * INFO: https://en.wikipedia.org/wiki/Byte_order_mark
     *
     * @var array<string, int>
     */
    private static $BOM = [
        "\xef\xbb\xbf"     => 3, // UTF-8 BOM
        'ï»¿'              => 6, // UTF-8 BOM as "WINDOWS-1252" (one char has [maybe] more then one byte ...)
        "\x00\x00\xfe\xff" => 4, // UTF-32 (BE) BOM
        '  þÿ'             => 6, // UTF-32 (BE) BOM as "WINDOWS-1252"
        "\xff\xfe\x00\x00" => 4, // UTF-32 (LE) BOM
        'ÿþ  '             => 6, // UTF-32 (LE) BOM as "WINDOWS-1252"
        "\xfe\xff"         => 2, // UTF-16 (BE) BOM
        'þÿ'               => 4, // UTF-16 (BE) BOM as "WINDOWS-1252"
        "\xff\xfe"         => 2, // UTF-16 (LE) BOM
        'ÿþ'               => 4, // UTF-16 (LE) BOM as "WINDOWS-1252"
    ];

    /**
     * Numeric code point => UTF-8 Character
     *
     * url: http://www.w3schools.com/charsets/ref_utf_punctuation.asp
     *
     * @var array<int, string>
     */
    private static $WHITESPACE = [
        // NULL Byte
        0 => "\x0",
        // Tab
        9 => "\x9",
        // New Line
        10 => "\xa",
        // Vertical Tab
        11 => "\xb",
        // Carriage Return
        13 => "\xd",
        // Ordinary Space
        32 => "\x20",
        // NO-BREAK SPACE
        160 => "\xc2\xa0",
        // OGHAM SPACE MARK
        5760 => "\xe1\x9a\x80",
        // MONGOLIAN VOWEL SEPARATOR
        6158 => "\xe1\xa0\x8e",
        // EN QUAD
        8192 => "\xe2\x80\x80",
        // EM QUAD
        8193 => "\xe2\x80\x81",
        // EN SPACE
        8194 => "\xe2\x80\x82",
        // EM SPACE
        8195 => "\xe2\x80\x83",
        // THREE-PER-EM SPACE
        8196 => "\xe2\x80\x84",
        // FOUR-PER-EM SPACE
        8197 => "\xe2\x80\x85",
        // SIX-PER-EM SPACE
        8198 => "\xe2\x80\x86",
        // FIGURE SPACE
        8199 => "\xe2\x80\x87",
        // PUNCTUATION SPACE
        8200 => "\xe2\x80\x88",
        // THIN SPACE
        8201 => "\xe2\x80\x89",
        // HAIR SPACE
        8202 => "\xe2\x80\x8a",
        // LINE SEPARATOR
        8232 => "\xe2\x80\xa8",
        // PARAGRAPH SEPARATOR
        8233 => "\xe2\x80\xa9",
        // NARROW NO-BREAK SPACE
        8239 => "\xe2\x80\xaf",
        // MEDIUM MATHEMATICAL SPACE
        8287 => "\xe2\x81\x9f",
        // HALFWIDTH HANGUL FILLER
        65440 => "\xef\xbe\xa0",
        // IDEOGRAPHIC SPACE
        12288 => "\xe3\x80\x80",
    ];

    /**
     * @var array<string, string>
     */
    private static $WHITESPACE_TABLE = [
        'SPACE'                     => "\x20",
        'NO-BREAK SPACE'            => "\xc2\xa0",
        'OGHAM SPACE MARK'          => "\xe1\x9a\x80",
        'EN QUAD'                   => "\xe2\x80\x80",
        'EM QUAD'                   => "\xe2\x80\x81",
        'EN SPACE'                  => "\xe2\x80\x82",
        'EM SPACE'                  => "\xe2\x80\x83",
        'THREE-PER-EM SPACE'        => "\xe2\x80\x84",
        'FOUR-PER-EM SPACE'         => "\xe2\x80\x85",
        'SIX-PER-EM SPACE'          => "\xe2\x80\x86",
        'FIGURE SPACE'              => "\xe2\x80\x87",
        'PUNCTUATION SPACE'         => "\xe2\x80\x88",
        'THIN SPACE'                => "\xe2\x80\x89",
        'HAIR SPACE'                => "\xe2\x80\x8a",
        'LINE SEPARATOR'            => "\xe2\x80\xa8",
        'PARAGRAPH SEPARATOR'       => "\xe2\x80\xa9",
        'ZERO WIDTH SPACE'          => "\xe2\x80\x8b",
        'NARROW NO-BREAK SPACE'     => "\xe2\x80\xaf",
        'MEDIUM MATHEMATICAL SPACE' => "\xe2\x81\x9f",
        'IDEOGRAPHIC SPACE'         => "\xe3\x80\x80",
        'HALFWIDTH HANGUL FILLER'   => "\xef\xbe\xa0",
    ];

    /**
     * @var array
     *
     * @phpstan-var array{upper: string[], lower: string[]}
     */
    private static $COMMON_CASE_FOLD = [
        'upper' => [
            'µ',
            'ſ',
            "\xCD\x85",
            'ς',
            'ẞ',
            "\xCF\x90",
            "\xCF\x91",
            "\xCF\x95",
            "\xCF\x96",
            "\xCF\xB0",
            "\xCF\xB1",
            "\xCF\xB5",
            "\xE1\xBA\x9B",
            "\xE1\xBE\xBE",
        ],
        'lower' => [
            'μ',
            's',
            'ι',
            'σ',
            'ß',
            'β',
            'θ',
            'φ',
            'π',
            'κ',
            'ρ',
            'ε',
            "\xE1\xB9\xA1",
            'ι',
        ],
    ];

    /**
     * @var array
     *
     * @phpstan-var array<string, mixed>
     */
    private static $SUPPORT = [];

    /**
     * @var string[]|null
     *
     * @phpstan-var array<string, string>|null
     */
    private static $BROKEN_UTF8_FIX;

    /**
     * @var string[]|null
     *
     * @phpstan-var array<int, string>|null
     */
    private static $WIN1252_TO_UTF8;

    /**
     * @var string[]|null
     *
     * @phpstan-var array<int ,string>|null
     */
    private static $INTL_TRANSLITERATOR_LIST;

    /**
     * @var string[]|null
     *
     * @phpstan-var array<string>|null
     */
    private static $ENCODINGS;

    /**
     * @var int[]|null
     *
     * @phpstan-var array<string ,int>|null
     */
    private static $ORD;

    /**
     * @var string[]|null
     *
     * @phpstan-var array<string, string>|null
     */
    private static $EMOJI;

    /** @var array<string>|null */
    private static ?array $EMOJI_ENCODE_KEYS_CACHE = null;
    private static ?array $EMOJI_ENCODE_VALUES_CACHE = null;

    /** @var array<string>|null */
    private static ?array $EMOJI_DECODE_KEYS_CACHE = null;
    private static ?array $EMOJI_DECODE_VALUES_CACHE = null;

    /** @var array<string>|null */
    private static ?array $EMOJI_KEYS_REVERSIBLE_CACHE = null;

    /**
     * @var string[]|null
     *
     * @phpstan-var array<int, string>|null
     */
    private static $CHR;

    /**
     * Auto-detects the server environment for UTF-8 support.
     *
     * @return bool|null True if support was detected, null if already checked.
     *
     * @internal No need to run manually, it is triggered if needed.
     */
    public static function checkForSupport(): ?bool
    {
        if (isset(self::$SUPPORT['alreadyCheckedViaPortableUtf8'])) {
            return null;
        }

        self::$SUPPORT['alreadyCheckedViaPortableUtf8'] = true;

        // Loop through the check map and perform checks
        foreach (self::FEATURE_MAP as $key => $type) {
            switch ($type) {
                case self::FEATURE_TYPE_EXTENSION:
                    self::$SUPPORT[$key] = self::isExtensionLoaded($key);
                    break;
                case self::FEATURE_TYPE_FUNCTION:
                    self::$SUPPORT[$key] = self::isFunctionExists($key);
                    break;
                case self::FEATURE_TYPE_CLASS:
                    self::$SUPPORT[$key] = self::isClassExists($key);
                    break;
            }
        }

        // Check for PCRE UTF-8 support
        self::$SUPPORT[self::FEATURE_PCREUTF8] = self::hasPcreUtf8Support();

        self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] = self::isMbstringOverloaded();

        // Special handling for mbstring encoding setup if mbstring is loaded
        if (self::$SUPPORT[self::FEATURE_TYPE_EXTENSION]) {
            mb_internal_encoding(self::UTF8);
            mb_regex_encoding(self::UTF8);
            self::$SUPPORT[self::FEATURE_MBSTRING_INTERNAL] = self::UTF8;
        }

        // Check for Symfony polyfill usage
        self::$SUPPORT[self::FEATURE_SYMFONY_POLYFILL] = self::isSymfonyPolyfillUsed();

        if (self::$SUPPORT[self::FEATURE_SYMFONY_POLYFILL]) {
            mb_internal_encoding(self::UTF8);
            self::$SUPPORT[self::FEATURE_MBSTRING_INTERNAL] = self::UTF8;
        }

        return true;
    }

    /**
     * Checks if the \u modifier is available for Unicode support in PCRE.
     *
     * @psalm-pure
     *
     * @return bool True if support is available, false otherwise.
     */
    public static function hasPcreUtf8Support(): bool
    {
        // Check if PCRE supports the \u modifier for Unicode
        return preg_match('//u', '') !== false;
    }

    /**
     * Checks whether Symfony polyfills are used.
     *
     * @psalm-pure
     *
     * @return bool True if in use, false otherwise.
     *
     * @internal This will be made private in the next major version.
     */
    public static function isSymfonyPolyfillUsed(): bool
    {
        return (
            (!self::isExtensionLoaded(self::FEATURE_MBSTRING) && self::isFunctionExists('mb_strlen')) ||
            (!self::isExtensionLoaded(self::FEATURE_ICONV) && self::isFunctionExists('iconv'))
        );
    }

    /**
     * Checks whether mbstring "overloaded" is active on the server.
     *
     * @psalm-pure
     *
     * @return bool True if mbstring is overloaded, false otherwise.
     */
    private static function isMbstringOverloaded(): bool
    {
        // Check if PHP version is 8.0 or higher, as 'mbstring.func_overload' was removed in PHP 8.
        if (PHP_VERSION_ID >= 80000) {
            return false;
        }

        // Check if the 'mbstring.func_overload' setting is greater than 0
        return (int) ini_get('mbstring.func_overload') > 0;
    }

    /**
     * Returns the character at the specified position, similar to $str[1] functionality.
     *
     * Example: UTF8::charAt('fòô', 1); // 'ò'
     *
     * @param string $str      A UTF-8 string.
     * @param int    $pos      The position of the character to return.
     * @param string $encoding [optional] The character encoding (default: UTF-8).
     *
     * @psalm-pure
     *
     * @return string A single multi-byte character or an empty string if out of bounds.
     */
    public static function charAt(string $str, int $pos, string $encoding = self::UTF8): string
    {
        if ($pos < 0 || $pos >= \mb_strlen($str, $encoding)) {
            return '';
        }

        return $encoding === self::UTF8
            ? \mb_substr($str, $pos, 1)
            : self::substr($str, $pos, 1, $encoding);
    }

    /**
     * Prepends a UTF-8 BOM character to the string if it does not already have one.
     *
     * Example: UTF8::addBomToString('fòô'); // "\xEF\xBB\xBF" . 'fòô'
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return non-empty-string The output string containing the BOM.
     */
    public static function addBomToString(string $str): string
    {
        return self::hasBom($str) ? $str : self::bom() . $str;
    }

    /**
     * Changes the case of all keys in an array.
     *
     * @param array<string, mixed> $array The input array.
     * @param int $case [optional] Either CASE_UPPER or CASE_LOWER (default: CASE_LOWER).
     * @param string $encoding [optional] The character encoding for string conversion (default: UTF-8).
     *
     * @psalm-pure
     *
     * @return array<string, mixed> An array with its keys converted to lower- or uppercase.
     */
    public static function changeArrayKeyCase(
        array $array,
        int $case = CASE_LOWER,
        string $encoding = self::UTF8
    ): array {
        if ($case !== CASE_LOWER && $case !== CASE_UPPER) {
            $case = CASE_LOWER;
        }

        $convertCase = $case === CASE_LOWER ? 'strtolower' : 'strtoupper';

        $result = [];
        foreach ($array as $key => $value) {
            $result[self::$convertCase($key, $encoding)] = $value;
        }

        return $result;
    }

    /**
     * Returns the substring between `$start` and `$end`, if found, or an empty string.
     * An optional offset may be supplied from which to begin the search.
     *
     * @param string $str The input string.
     * @param string $start The delimiter marking the start of the substring.
     * @param string $end The delimiter marking the end of the substring.
     * @param int $offset [optional] The index from which to begin the search (default: 0).
     * @param string $encoding [optional] The character encoding for string functions (default: UTF-8).
     *
     * @psalm-pure
     *
     * @return string The extracted substring or an empty string if not found.
     */
    public static function getSubstringBetween(
        string $str,
        string $start,
        string $end,
        int $offset = 0,
        string $encoding = self::UTF8
    ): string {
        $useMbFunctions = ($encoding === self::UTF8);
        
        $strpos = $useMbFunctions ? 'mb_strpos' : [self::class, 'strpos'];
        $strlen = $useMbFunctions ? 'mb_strlen' : [self::class, 'strlen'];
        $substr = $useMbFunctions ? 'mb_substr' : [self::class, 'substr'];

        $startPos = $strpos($str, $start, $offset, $encoding);
        if ($startPos === false) {
            return '';
        }

        $substrIndex = $startPos + (int) $strlen($start, $encoding);
        $endPos = $strpos($str, $end, $substrIndex, $encoding);
        
        return ($endPos === false || $endPos === $substrIndex) 
            ? '' 
            : (string) $substr($str, $substrIndex, $endPos - $substrIndex, $encoding);
    }

    /**
     * Converts a binary string into a UTF-8 string.
     *
     * Opposite of `UTF8::strToBinary()`.
     *
     * Example: UTF8::binaryToString('11110000100111111001100010000011'); // '😃'
     *
     * @param string $binary A binary string (1s and 0s).
     *
     * @psalm-pure
     *
     * @return string The decoded UTF-8 string.
     */
    public static function binaryToString(string $binary): string
    {
        if ($binary === '') {
            return '';
        }

        $hex = base_convert($binary, 2, 16);
        
        return ($hex === '0') ? '' : pack('H*', $hex);
    }

    /**
     * Returns the UTF-8 Byte Order Mark Character.
     *
     * INFO: take a look at UTF8::$bom for e.g. UTF-16 and UTF-32 BOM values
     *
     * EXAMPLE: <code>UTF8::bom(); // "\xEF\xBB\xBF"</code>
     *
     * @psalm-pure
     *
     * @return non-empty-string
     *                           <p>UTF-8 Byte Order Mark.</p>
     */
    public static function bom(): string
    {
        return "\xef\xbb\xbf";
    }

    /**
     * Alias of UTF8::chrMap().
     *
     * @param callable(string): string $callback
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string[]
     *
     * @see UTF8::chrMap()
     */
    public static function callback(callable $callback, string $str): array
    {
        return self::chrMap($callback, $str);
    }

    /**
     * Returns an array consisting of the characters in the string.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return string[]
     *                  <p>An array of chars.</p>
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-list<string> : list<string>)
     */
    public static function chars(string $str): array
    {
        return self::strSplit($str);
    }

    /**
     * Generates a UTF-8 encoded character from the given code point.
     *
     * @param int    $codePoint The Unicode code point.
     * @param string $encoding  [optional] Character encoding, default is UTF-8.
     *
     * @psalm-pure
     *
     * @return string|null UTF-8 character, or null on failure.
     */
    public static function chr(int $codePoint, string $encoding = self::UTF8): ?string
    {
        static $charCache = [];

        if ($codePoint <= 0) {
            return null;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }

        if (!in_array($encoding, [self::UTF8, self::ISO88591, self::WINDOWS1252], true) && !self::$SUPPORT[self::FEATURE_MBSTRING]) {
            trigger_error('chr() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
        }

        $cacheKey = $codePoint . '_' . $encoding;
        if (isset($charCache[$cacheKey])) {
            return $charCache[$cacheKey];
        }

        // Handle ASCII characters efficiently
        if ($codePoint <= 0x80) {
            self::$CHR ??= self::getData('chr');
            $chr = self::$CHR[$codePoint] ?? '';

            return $charCache[$cacheKey] = $encoding === self::UTF8 ? $chr : self::encode($encoding, $chr);
        }

        // Use IntlChar if available
        if (self::$SUPPORT[self::FEATURE_INTLCHAR]) {
            $chr = IntlChar::chr($codePoint);

            return $charCache[$cacheKey] = $encoding === self::UTF8 ? $chr : self::encode($encoding, $chr);
        }

        // Use a manual UTF-8 encoding fallback
        self::$CHR ??= self::getData('chr');

        if ($codePoint <= 0x7FF) {
            $chr = self::$CHR[($codePoint >> 6) + 0xC0] . self::$CHR[($codePoint & 0x3F) + 0x80];
        } elseif ($codePoint <= 0xFFFF) {
            $chr = self::$CHR[($codePoint >> 12) + 0xE0] .
                self::$CHR[(($codePoint >> 6) & 0x3F) + 0x80] .
                self::$CHR[($codePoint & 0x3F) + 0x80];
        } else {
            $chr = self::$CHR[($codePoint >> 18) + 0xF0] .
                self::$CHR[(($codePoint >> 12) & 0x3F) + 0x80] .
                self::$CHR[(($codePoint >> 6) & 0x3F) + 0x80] .
                self::$CHR[($codePoint & 0x3F) + 0x80];
        }

        return $charCache[$cacheKey] = $encoding === self::UTF8 ? $chr : self::encode($encoding, $chr);
    }

    /**
     * Applies a callback to all characters of a UTF-8 string.
     *
     * Example: UTF8::chrMap([UTF8::class, 'toLowerCase'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']
     *
     * @param callable(string): string $callback The callback function.
     * @param string $str The UTF-8 string to apply the callback on.
     *
     * @psalm-pure
     *
     * @return string[] The result of the callback applied to each character.
     */
    public static function chrMap(callable $callback, string $str): array
    {
        if ($str === '') {
            return [];
        }

        return array_map($callback, self::strSplit($str));
    }

    /**
     * Generates an array representing the byte length of each character in a Unicode string.
     *
     * 1 byte => U+0000  - U+007F
     * 2 byte => U+0080  - U+07FF
     * 3 byte => U+0800  - U+FFFF
     * 4 byte => U+10000 - U+10FFFF
     *
     * Example: UTF8::chrSizeList('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]
     *
     * @param string $str The original Unicode string.
     *
     * @psalm-pure
     *
     * @return int[] An array of byte lengths of each character.
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-list<1|2|3|4> : list<1|2|3|4>)
     */
    public static function chrSizeList(string $str): array
    {
        if ($str === '') {
            return [];
        }

        $chars = self::strSplit($str);

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD]) {
            return array_map(
                static fn(string $char): int => mb_strlen($char, self::CP850),
                $chars
            );
        }

        return array_map('strlen', $chars);
    }

    /**
     * Returns the decimal code representation of a specific character.
     *
     * INFO: Opposite to UTF8::decimalToChr()
     *
     * Example: UTF8::chrToDecimal('§'); // 0xa7
     *
     * @param string $char The input character.
     *
     * @psalm-pure
     *
     * @return int The decimal Unicode code point.
     */
    public static function chrToDecimal(string $char): int
    {
        if (self::$SUPPORT[self::FEATURE_ICONV]) {
            $converted = iconv(self::UTF8, 'UCS-4LE', $char);
            if ($converted !== false) {
                /** @phpstan-ignore-next-line - "unpack" only returns false if the format string contains errors */
                return unpack('V', $converted)[1];
            }
        }

        $code = self::ord($char[0]);
        
        if ($code < 0x80) {
            return $code; // Single-byte (ASCII) character
        }

        $bytes = match (true) {
            ($code & 0xE0) === 0xC0 => 2, // 110xxxxx
            ($code & 0xF0) === 0xE0 => 3, // 1110xxxx
            ($code & 0xF8) === 0xF0 => 4, // 11110xxx
            default => 1 // Invalid or ASCII
        };

        $code &= [0, 0x1F, 0x0F, 0x07][$bytes];

        for ($i = 1; $i < $bytes; ++$i) {
            $code = ($code << 6) + (self::ord($char[$i]) & 0x3F);
        }

        return $code;
    }

    /**
     * Returns the hexadecimal code point (U+xxxx) of a UTF-8 encoded character.
     *
     * Example: UTF8::chrToHex('§'); // U+00A7
     *
     * @param int|string $char The input character.
     * @param string $prefix [optional] The prefix for the output format. Default: 'U+'.
     *
     * @psalm-pure
     *
     * @return string The code point encoded as U+xxxx.
     */
    public static function chrToHex(int|string $char, string $prefix = 'U+'): string
    {
        if ($char === '' || $char === '&#0;') {
            return '';
        }

        return self::intToHex(self::ord((string) $char), $prefix);
    }

    /**
     * Splits a string into smaller chunks and multiple lines, using the specified line ending character.
     *
     * Example: UTF8::chunkSplit('ABC-ÖÄÜ-中文空白-κόσμε', 3); // "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε"
     *
     * @param string $str The original string to be split.
     * @param int $chunkLength [optional] The maximum character length of a chunk. Default: 76.
     * @param string $end [optional] The character(s) to be inserted at the end of each chunk. Default: "\r\n".
     *
     * @psalm-pure
     *
     * @return string The chunked string.
     */
    public static function chunkSplit(string $str, int $chunkLength = 76, string $end = "\r\n"): string
    {
        return implode($end, self::strSplit($str, $chunkLength));
    }

    /**
     * Cleans a string by removing non-UTF-8 characters and applying optional normalizations.
     *
     * Example: UTF8::clean("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true);
     * // Output: '„Abcdef  …” — 😃 - DÃ¼sseldorf'
     *
     * @param string $str The string to be sanitized.
     * @param bool $removeBom Whether to remove UTF-BOM. Default: false.
     * @param bool $normalizeWhitespace Whether to normalize whitespace. Default: false.
     * @param bool $normalizeMsWord Whether to normalize MS Word characters. Default: false.
     * @param bool $keepNonBreakingSpace Whether to keep non-breaking spaces with whitespace normalization. Default: false.
     * @param bool $replaceDiamondQuestionMark Whether to remove the "�" character. Default: false.
     * @param bool $removeInvisibleCharacters Whether to remove invisible characters. Default: true.
     * @param bool $removeInvisibleCharactersUrlEncoded Whether to remove invisible URL-encoded characters. Default: false.
     *
     * @psalm-pure
     *
     * @return string A cleaned UTF-8 encoded string.
     */
    public static function clean(
        string $str,
        bool $removeBom = false,
        bool $normalizeWhitespace = false,
        bool $normalizeMsWord = false,
        bool $keepNonBreakingSpace = false,
        bool $replaceDiamondQuestionMark = false,
        bool $removeInvisibleCharacters = true,
        bool $removeInvisibleCharactersUrlEncoded = false
    ): string {
        // Remove non-UTF-8 characters using regex
        $regex = '/
        (
            (?: [\x00-\x7F]              # Single-byte sequences (0xxxxxxx)
            |   [\xC0-\xDF][\x80-\xBF]   # Double-byte sequences (110xxxxx 10xxxxxx)
            |   [\xE0-\xEF][\x80-\xBF]{2} # Triple-byte sequences (1110xxxx 10xxxxxx * 2)
            |   [\xF0-\xF7][\x80-\xBF]{3} # Quadruple-byte sequences (11110xxx 10xxxxxx * 3)
            ){1,100}                     # Match multiple characters at once
        )
        | ( [\x80-\xBF] )                # Invalid byte (10000000 - 10111111)
        | ( [\xC0-\xFF] )                # Invalid leading byte (11000000 - 11111111)
        /x';

        $str = (string) preg_replace($regex, '$1', $str);

        if ($replaceDiamondQuestionMark) {
            $str = self::replaceDiamondQuestionMark($str);
        }

        if ($removeInvisibleCharacters) {
            $str = self::removeInvisibleCharacters($str, $removeInvisibleCharactersUrlEncoded);
        }

        if ($normalizeWhitespace) {
            $str = self::normalizeWhitespace($str, $keepNonBreakingSpace);
        }

        if ($normalizeMsWord) {
            $str = self::normalizeMsWord($str);
        }

        if ($removeBom) {
            $str = self::removeBom($str);
        }

        return $str;
    }

    /**
     * Clean-up a string and show only printable UTF-8 chars at the end + fix UTF-8 encoding.
     *
     * EXAMPLE: <code>UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - Düsseldorf'</code>
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function cleanup(string $str): string
    {
        if ($str === '') {
            return '';
        }

        // Fix simple UTF-8 encoding issues
        $str = self::fixSimpleUtf8($str);

        // Clean the string by removing non-UTF-8 symbols, BOM, invisible chars, and normalizing whitespace
        return self::clean(
            $str,
            removeBom: true,
            normalizeWhitespace: true,
            normalizeMsWord: false,
            keepNonBreakingSpace: true,
            replaceDiamondQuestionMark: false,
            removeInvisibleCharacters: true,
            removeInvisibleCharactersUrlEncoded: false
        );
    }

    /**
     * Accepts a string or an array of chars and returns an array of Unicode code points.
     *
     * INFO: Opposite to UTF8::string().
     *
     * EXAMPLE: <code>
     * UTF8::codepoints('κöñ'); // array(954, 246, 241)
     * // ... OR ...
     * UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
     * </code>
     *
     * @param string|string[] $arg         <p>A UTF-8 encoded string or an array of chars.</p>
     * @param bool            $useUStyle   <p>If true, will return code points in U+xxxx format,
     *                                     default, code points will be returned as integers.</p>
     *
     * @psalm-pure
     *
     * @return int[]|string[]
     *                        <p>
     *                        The array of code points:<br>
     *                        int[] for $useUStyle === false<br>
     *                        string[] for $useUStyle === true<br>
     *                        </p>
     */
    public static function codepoints($arg, bool $useUStyle = false): array
    {
        if (is_string($arg)) {
            $arg = self::strSplit($arg);
        }

        if (!is_array($arg) || empty($arg)) {
            return [];
        }

        // Use array_map for efficiency to apply ord or intToHex to each element
        $arg = array_map([self::class, 'ord'], $arg);

        if ($useUStyle) {
            $arg = array_map([self::class, 'intToHex'], $arg);
        }

        return $arg;
    }

    /**
     * Trims the string and replaces consecutive whitespace characters with a
     * single space. This includes tabs and newline characters, as well as
     * multibyte whitespace such as the thin space and ideographic space.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with trimmed $str and condensed whitespace.</p>
     */
    public static function collapseWhitespace(string $str): string
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return trim((string) mb_ereg_replace('[[:space:]]+', ' ', $str));
        }

        return trim(self::regexReplace($str, '[[:space:]]+', ' '));
    }

    /**
     * Returns the count of characters used in a string.
     *
     * EXAMPLE: <code>UTF8::countChars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)</code>
     *
     * @param string $str                     <p>The input string.</p>
     * @param bool   $cleanUtf8              [optional] <p>Remove non-UTF-8 chars from the string.</p>
     * @param bool   $tryToUseMbFunctions    [optional] <p>Set to false if you don't want to use multibyte functions.</p>
     *
     * @psalm-pure
     *
     * @return int[] 
     *               <p>An associative array of characters as keys and their count as values.</p>
     */
    public static function countChars(
        string $str,
        bool $cleanUtf8 = false,
        bool $tryToUseMbFunctions = true
    ): array {
        // Split the string into an array of characters
        $chars = self::strSplit($str, 1, $cleanUtf8, $tryToUseMbFunctions);

        // Count the occurrences of each character and return the result
        return array_count_values($chars);
    }

    /**
     * Create a valid CSS identifier for "class" or "id" attributes.
     *
     * EXAMPLE: <code>UTF8::cssIdentifier('123foo/bar!!!'); // _23foo-bar</code>
     *
     * @param string   $str        <p>INFO: If no identifier is given (e.g., " " or ""), a unique string will be created automatically.</p>
     * @param string[] $filter     <p>A map of characters to be replaced in the identifier.</p>
     * @param bool     $stripTags  <p>If true, HTML tags will be removed from the string.</p>
     * @param bool     $strtolower <p>If true, the string will be converted to lowercase.</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function cssIdentifier(
        string $str = '',
        array $filter = [
            ' ' => '-',
            '/' => '-',
            '[' => '',
            ']' => '',
        ],
        bool $stripTags = false,
        bool $strtolower = true
    ): string {
        // Trim and clean the string, if necessary
        $str = trim($str);
        if ($str) {
            $str = self::clean($str, true);
        }

        // Optionally strip HTML tags
        if ($stripTags) {
            $str = strip_tags($str);
        }

        // If the string is still empty, generate a unique identifier
        if (!$str) {
            $str = uniqid('auto-generated-css-class', true);
        }

        // Convert to lowercase if required
        if ($strtolower) {
            $str = strtolower($str);
        }

        // Handle double underscores if not in the filter
        $doubleUnderscoreReplacements = 0;
        if (!isset($filter['__'])) {
            $str = str_replace('__', '##', $str, $doubleUnderscoreReplacements);
        }

        // Replace characters based on the filter map
        $str = str_replace(array_keys($filter), array_values($filter), $str);

        // Replace the temporary placeholder with double underscores if needed
        if ($doubleUnderscoreReplacements > 0) {
            $str = str_replace('##', '__', $str);
        }

        // Remove invalid characters and ensure valid CSS identifier
        $str = preg_replace('/[^\x{002D}\x{0030}-\x{0039}\x{0041}-\x{005A}\x{005F}\x{0061}-\x{007A}\x{00A1}-\x{FFFF}]/u', '', $str);

        // Ensure the identifier doesn't start with a digit or invalid character
        $str = preg_replace(['/^[0-9]/', '/^(-[0-9])|^(--)/'], ['_', '__'], $str);

        return trim($str, '-');
    }

    /**
     * Remove CSS media queries from the given string.
     *
     * @param string $str The input string containing CSS.
     *
     * @psalm-pure
     *
     * @return string The input string with media queries removed.
     */
    public static function cssStripMediaQueries(string $str): string
    {
        return preg_replace('#@media\\s+(?:only\\s)?(?:[\\s{(]|screen|all)\\s?[^{]+{.*}\\s*}\\s*#isumU', '', $str) ?: '';
    }

    /**
     * Converts an integer value into a UTF-8 character.
     *
     * INFO: Opposite of UTF8::string().
     *
     * EXAMPLE: <code>UTF8::decimalToChr(931); // 'Σ'</code>
     *
     * @param int|string $int The integer value to convert.
     *
     * @phpstan-param int|numeric-string $int
     *
     * @psalm-pure
     *
     * @return string The UTF-8 character corresponding to the given integer.
     */
    public static function decimalToChr($int): string
    {
        // We cannot use html_entity_decode() here, as it will not return
        // characters for many values < 160.
        return mb_convert_encoding('&#' . $int . ';', self::UTF8, self::HTML_ENTITIES);
    }

    /**
     * Decodes a MIME header field
     *
     * @param string $str
     * @param string $encoding [optional] Set the charset for e.g. "mb_" function
     *
     * @psalm-pure
     *
     * @return false|string A decoded MIME field on success, or false if an error occurs during decoding.
     */
    public static function decodeMimeHeader(string $str, string $encoding = self::UTF8)
    {
        // Validate encoding
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }

        // Use the symfony polyfill for fallback
        return iconv_mime_decode($str, ICONV_MIME_DECODE_CONTINUE_ON_ERROR, $encoding);
    }


    /**
     * Convert any two-letter country code (ISO 3166-1) to the corresponding Emoji.
     *
     * @see https://en.wikipedia.org/wiki/ISO_3166-1
     *
     * @param string $countryCodeIso3166_1 Two-letter country code (e.g., 'DE').
     *
     * @return string Emoji or empty string on error.
     */
    public static function emojiFromCountryCode(string $countryCodeIso3166_1): string
    {
        // Ensure the country code is valid and has two characters
        $countryCodeIso3166_1 = strtoupper($countryCodeIso3166_1);
        if (strlen($countryCodeIso3166_1) !== 2) {
            return '';
        }

        $flagOffset = 0x1F1E6;
        $asciiOffset = 0x41;

        $firstChar = ord($countryCodeIso3166_1[0]) - $asciiOffset + $flagOffset;
        $secondChar = ord($countryCodeIso3166_1[1]) - $asciiOffset + $flagOffset;

        // Return the emoji, or an empty string if invalid characters are encountered
        return (chr($firstChar) ?? '') . (chr($secondChar) ?? '');
    }

    /**
     * Encodes a string with emoji characters into a non-emoji representation.
     */
    public static function emojiEncode(string $str, bool $useReversibleStringMappings = false): string
    {
        self::initEmojiEncodeData();

        $keysCache = $useReversibleStringMappings ? self::$EMOJI_KEYS_REVERSIBLE_CACHE : self::$EMOJI_ENCODE_KEYS_CACHE;

        return str_replace((array) self::$EMOJI_ENCODE_VALUES_CACHE, (array) $keysCache, $str);
    }

    /**
     * Decodes a string encoded by emojiEncode().
     */
    public static function emojiDecode(string $str, bool $useReversibleStringMappings = false): string
    {
        self::initEmojiDecodeData();

        $keysCache = $useReversibleStringMappings ? self::$EMOJI_KEYS_REVERSIBLE_CACHE : self::$EMOJI_DECODE_KEYS_CACHE;

        return str_replace((array) $keysCache, (array) self::$EMOJI_DECODE_VALUES_CACHE, $str);
    }

    /**
     * Encode a string with a new charset-encoding.
     *
     * INFO:  This function will also try to fix broken / double encoding,
     *        so you can call this function also on a UTF-8 string and you don't mess up the string.
     *
     * EXAMPLE: <code>
     * UTF8::encode('ISO-8859-1', '-ABC-中文空白-'); // '-ABC-????-'
     * //
     * UTF8::encode('UTF-8', '-ABC-中文空白-'); // '-ABC-中文空白-'
     * //
     * UTF8::encode('HTML', '-ABC-中文空白-'); // '-ABC-&#20013;&#25991;&#31354;&#30333;-'
     * //
     * UTF8::encode('BASE64', '-ABC-中文空白-'); // 'LUFCQy3kuK3mlofnqbrnmb0t'
     * </code>
     *
     * @param string $toEncoding                    <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.</p>
     * @param string $str                           <p>The input string</p>
     * @param bool   $autoDetectFromEncoding        [optional] <p>Force the new encoding (we try to fix broken / double
     *                                              encoding for UTF-8)<br> otherwise we auto-detect the current
     *                                              string-encoding</p>
     * @param string $fromEncoding                  [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
     *                                              A empty string will trigger the autodetect anyway.</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @psalm-suppress InvalidReturnStatement
     */
    public static function encode(
        string $toEncoding,
        string $str,
        bool $autoDetectFromEncoding = true,
        string $fromEncoding = ''
    ): string {
        if ($str === '' || $toEncoding === '') {
            return $str;
        }
    
        // Normalize encoding names if needed
        if ($toEncoding !== self::UTF8 && $toEncoding !== self::CP850) {
            $toEncoding = self::normalizeEncoding($toEncoding, self::UTF8);
        }
    
        if ($fromEncoding && $fromEncoding !== self::UTF8 && $fromEncoding !== self::CP850) {
            $fromEncoding = self::normalizeEncoding($fromEncoding, self::UTF8);
        }
    
        // If the source and target encodings are the same, return the input string
        if ($fromEncoding === $toEncoding) {
            return $str;
        }
    
        // Handle specific input encoding conversions
        switch ($fromEncoding) {
            case self::JSON:
                $str = self::jsonDecode($str);
                $fromEncoding = '';
                break;
    
            case self::BASE64:
                $str = base64_decode($str, true);
                $fromEncoding = '';
                break;
    
            case self::HTML_ENTITIES:
                $str = self::htmlEntityDecode($str, ENT_COMPAT);
                $fromEncoding = '';
                break;
        }
    
        // Handle specific output encoding conversions
        switch ($toEncoding) {
            case self::JSON:
                $encoded = self::jsonEncode($str);
                if ($encoded === false) {
                    throw new InvalidArgumentException("The input string [$str] cannot be used for jsonEncode().");
                }
                return $encoded;
    
            case self::BASE64:
                return base64_encode($str);
    
            case self::HTML_ENTITIES:
                return self::htmlEncode($str, true);
        }
    
        // Auto-detect input encoding if needed
        if ($autoDetectFromEncoding || !$fromEncoding) {
            $detectedEncoding = self::detectStringEncoding($str);
            $fromEncoding = $detectedEncoding !== false ? $detectedEncoding : '';
        }
    
        // Fallback for autodetect mode
        if (!$fromEncoding) {
            return self::toUtf8($str);
        }
    
        // Common conversions for UTF-8 and ISO-8859-1
        if ($toEncoding === self::UTF8 && ($fromEncoding === self::WINDOWS1252 || $fromEncoding === self::ISO88591)) {
            return self::toUtf8($str);
        }
    
        if ($toEncoding === self::ISO88591 && ($fromEncoding === self::WINDOWS1252 || $fromEncoding === self::UTF8)) {
            return self::toIso8859($str);
        }
    
        // Ensure `mbstring` is available for more complex conversions
        if (!in_array($toEncoding, [self::UTF8, self::ISO88591, self::WINDOWS1252], true) && !self::$SUPPORT[self::FEATURE_MBSTRING]) {
            trigger_error("UTF8::encode() without mbstring cannot handle \"$toEncoding\" encoding", E_USER_WARNING);
        }
    
        // Use `mb_convert_encoding` if available
        if (self::$SUPPORT[self::FEATURE_MBSTRING]) {
            $encodedStr = mb_convert_encoding($str, $toEncoding, $fromEncoding);
            if ($encodedStr !== false) {
                return $encodedStr;
            }
        }
    
        // Fallback to `iconv`
        $convertedStr = @iconv($fromEncoding, $toEncoding, $str);
        return $convertedStr !== false ? $convertedStr : $str;
    }

    /**
     * Encodes a MIME header field using the specified character set and encoding.
     *
     * @param string      $str               The input string to encode.
     * @param string      $fromCharset       [optional] The input charset (default: UTF-8).
     * @param string      $toCharset         [optional] The output charset (default: UTF-8).
     * @param string      $transferEncoding  [optional] The transfer encoding scheme (default: 'Q' for quoted-printable).
     * @param string      $linefeed          [optional] The line break sequence (default: "\r\n").
     * @param int<1, max> $indent            [optional] The maximum line length (default: 76).
     *
     * @psalm-pure
     *
     * @return false|string Encoded MIME field on success, or false on failure.
     */
    public static function encodeMimeHeader(
        string $str,
        string $fromCharset = self::UTF8,
        string $toCharset = self::UTF8,
        string $transferEncoding = 'Q',
        string $linefeed = "\r\n",
        int $indent = 76
    ) {
        // Normalize character encodings if not UTF-8 or CP850
        if (!in_array($fromCharset, [self::UTF8, self::CP850], true)) {
            $fromCharset = self::normalizeEncoding($fromCharset, self::UTF8);
        }

        if (!in_array($toCharset, [self::UTF8, self::CP850], true)) {
            $toCharset = self::normalizeEncoding($toCharset, self::UTF8);
        }

        // Encode MIME header
        return iconv_mime_encode(
            '',
            $str,
            [
                'scheme'           => $transferEncoding,
                'line-length'      => $indent,
                'input-charset'    => $fromCharset,
                'output-charset'   => $toCharset,
                'line-break-chars' => $linefeed,
            ]
        );
    }

    /**
     * Creates an extract from a sentence, centering on the search string if found.
     *
     * @param string   $str                  The input string.
     * @param string   $search               [optional] The searched string.
     * @param int|null $length               [optional] The extract length (default: half of text length).
     * @param string   $ellipsis             [optional] Placeholder for skipped text (default: …).
     * @param string   $encoding             [optional] Character encoding (default: UTF-8).
     *
     * @psalm-pure
     *
     * @return string The extracted text.
     */
    public static function extractText(
        string $str,
        string $search = '',
        ?int $length = null,
        string $ellipsis = '…',
        string $encoding = self::UTF8
    ): string {
        if ($str === '') {
            return '';
        }

        // Normalize encoding if needed
        if (!in_array($encoding, [self::UTF8, self::CP850], true)) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }

        $trimChars = "\t\r\n -_()!~?=+/*\\,.:;\"'[]{}`&";

        // Default extract length to half of string length
        if ($length === null) {
            $length = (int) round(mb_strlen($str, $encoding) / 2);
        }

        if ($search === '') {
            return self::extractWithoutSearch($str, $length, $ellipsis, $trimChars, $encoding);
        }

        return self::extractWithSearch($str, $search, $length, $ellipsis, $trimChars, $encoding);
    }

    /**
     * Extracts text when no search term is provided.
     */
    private static function extractWithoutSearch(
        string $str,
        int $length,
        string $ellipsis,
        string $trimChars,
        string $encoding
    ): string {
        $strLen = mb_strlen($str, $encoding);
        $end = min($length - 1, $strLen);
        
        $pos = min(
            mb_strpos($str, ' ', $end) ?: $strLen,
            mb_strpos($str, '.', $end) ?: $strLen
        );

        return rtrim(mb_substr($str, 0, $pos, $encoding), $trimChars) . $ellipsis;
    }

    /**
     * Extracts text centered around a search term.
     */
    private static function extractWithSearch(
        string $str,
        string $search,
        int $length,
        string $ellipsis,
        string $trimChars,
        string $encoding
    ): string {
        $wordPos = mb_stripos($str, $search, 0, $encoding);
        if ($wordPos === false) {
            return $str;
        }

        $halfSide = max(0, $wordPos - ($length / 2) + (mb_strlen($search, $encoding) / 2));
        
        $posStart = max(
            mb_strrpos(mb_substr($str, 0, $halfSide, $encoding), ' ') ?: 0,
            mb_strrpos(mb_substr($str, 0, $halfSide, $encoding), '.') ?: 0
        );

        $offset = min($posStart + $length - 1, mb_strlen($str, $encoding));
        
        $posEnd = min(
            mb_strpos($str, ' ', $offset) ?: mb_strlen($str, $encoding),
            mb_strpos($str, '.', $offset) ?: mb_strlen($str, $encoding)
        );

        $extract = mb_substr($str, $posStart, $posEnd - $posStart, $encoding);
        return $ellipsis . trim($extract, $trimChars) . $ellipsis;
    }

    /**
     * Reads an entire file into a string.
     *
     * WARNING: Do not enable UTF-8 conversion ($convertToUtf8) for binary files (e.g., images).
     *
     * @see https://www.php.net/manual/en/function.file-get-contents.php
     *
     * @param string        $filename        Name of the file to read.
     * @param bool          $useIncludePath  Whether to use include path for locating the file.
     * @param resource|null $context         A valid context resource or null.
     * @param int|null      $offset          The starting position for reading.
     * @param int|null      $maxLength       Maximum length of data to read (default: until end of file).
     * @param int           $timeout         Timeout in seconds.
     * @param bool          $convertToUtf8   Convert to UTF-8 encoding if necessary.
     * @param string        $fromEncoding    Source encoding (e.g., 'UTF-16', 'ISO-8859-1', etc.).
     *
     * @psalm-pure
     *
     * @return false|string The file contents as a string or false on failure.
     */
    public static function fileGetContents(
        string $filename,
        bool $useIncludePath = false,
        $context = null,
        ?int $offset = null,
        ?int $maxLength = null,
        int $timeout = 10,
        bool $convertToUtf8 = true,
        string $fromEncoding = ''
    ) {
        $filename = self::sanitizeFilename($filename);
        if ($filename === false) {
            return false;
        }

        if ($timeout && $context === null) {
            $context = stream_context_create([
                'http' => ['timeout' => $timeout]
            ]);
        }

        $offset = $offset ?? 0;
        $data = is_int($maxLength)
            ? file_get_contents($filename, $useIncludePath, $context, $offset, max(0, $maxLength))
            : file_get_contents($filename, $useIncludePath, $context, $offset);

        if ($data === false) {
            return false;
        }

        if ($convertToUtf8 && (self::requiresUtf8Conversion($data))) {
            $data = self::encodeToUtf8($data, $fromEncoding);
        }

        return $data;
    }

    /**
     * Checks if a file requires UTF-8 conversion.
     */
    private static function requiresUtf8Conversion(string $data): bool
    {
        return !self::isBinary($data, true) || self::isUtf16($data, false) || self::isUtf32($data, false);
    }

    /**
     * Converts data to UTF-8 and cleans it up.
     */
    private static function encodeToUtf8(string $data, string $fromEncoding): string
    {
        return self::cleanup(self::encode(self::UTF8, $data, false, $fromEncoding));
    }

    /**
     * Sanitizes the filename to prevent invalid input.
     */
    private static function sanitizeFilename(string $filename)
    {
        return self::filterSanitizeStringPolyfill($filename);
    }

    /**
     * Checks if a file starts with a BOM (Byte Order Mark) character.
     *
     * Example:
     * ```php
     * UTF8::fileHasBom('utf8_with_bom.txt'); // true
     * ```
     *
     * @param string $filePath The path to a valid file.
     *
     * @throws RuntimeException If file contents could not be retrieved.
     *
     * @return bool Returns `true` if the file starts with a BOM, `false` otherwise.
     *
     * @psalm-pure
     */
    public static function fileHasBom(string $filePath): bool
    {
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new RuntimeException("Failed to read file: {$filePath}");
        }

        return self::hasBom($fileContent);
    }

    /**
     * Normalizes a value to UTF-8 NFC, converting from WINDOWS-1252 when needed.
     *
     * This function ensures that text is in Unicode Normalization Form C (NFC), which is the standard 
     * for text storage and comparison. It can process arrays, objects, and strings, applying 
     * normalization recursively when necessary.
     *
     * Example:
     * ```php
     * UTF8::filter(["\xE9", 'à', 'a']); // Returns ['é', 'à', 'a']
     * ```
     *
     * @param array|object|string $var The value to be normalized.
     * @param int $normalizationForm The normalization form (default: NFC).
     * @param string $leadingCombining A special character to prevent leading combining marks.
     *
     * @return mixed The normalized value with the same type as the input.
     *
     * @psalm-pure
     */
    public static function filter(
        mixed $var,
        int $normalizationForm = Normalizer::NFC,
        string $leadingCombining = '◌'
    ): mixed {
        switch (gettype($var)) {
            case 'object':
            case 'array':
                // Recursively process each element in an array or object.
                foreach ($var as &$v) {
                    $v = self::filter($v, $normalizationForm, $leadingCombining);
                }
                unset($v); // Unset reference to avoid unexpected side effects.
                break;

            case 'string':
                // Normalize line endings to Unix-style if necessary.
                if (str_contains($var, "\r")) {
                    $var = self::normalizeLineEnding($var);
                }

                // If the string contains non-ASCII characters, proceed with normalization.
                if (!ASCII::isAscii($var)) {
                    // Check if the string is already in the desired normalization form.
                    if (Normalizer::isNormalized($var, $normalizationForm)) {
                        $normalized = '-'; // Marker indicating normalization was unnecessary.
                    } else {
                        // Attempt to normalize the string.
                        $normalized = Normalizer::normalize($var, $normalizationForm);

                        // If normalization fails or results in an empty string, fallback to manual UTF-8 encoding.
                        if (!$normalized || !isset($normalized[0])) {
                            $normalized = self::encode(self::UTF8, $var);
                        }
                    }

                    // Prevent leading combining characters for NFC-safe concatenations.
                    if (
                        $normalized &&               // Ensure the string is not empty.
                        $normalized[0] >= "\x80" &&  // Check if the first character is a non-ASCII byte.
                        isset($leadingCombining[0]) && 
                        preg_match('/^\p{Mn}/u', $normalized) // Check if it starts with a combining mark.
                    ) {
                        // Prepend a placeholder character to avoid leading combining marks.
                        $var = $leadingCombining . $normalized;
                    } else {
                        $var = $normalized;
                    }
                }
                break;
        }

        // Return the processed value, preserving the original type.
        /** @phpstan-var TFilter $var */
        return $var;
    }

    /**
     * A wrapper for `filter_input()` that normalizes input to UTF-8 NFC,
     * converting from WINDOWS-1252 when needed.
     *
     * Retrieves an external variable by name and applies optional filtering.
     *
     * EXAMPLE:
     * ```php
     * // Assuming $_GET['foo'] = 'bar';
     * UTF8::filterInput(INPUT_GET, 'foo', FILTER_UNSAFE_RAW); // Returns 'bar'
     * ```
     *
     * @see https://www.php.net/manual/en/function.filter-input.php
     *
     * @param int            $type         One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
     * @param string         $variableName The name of the variable to retrieve.
     * @param int            $filter       [optional] The filter ID to apply. Defaults to FILTER_DEFAULT.
     * @param int|int[]|null $options      [optional] Associative array of options or bitwise flags.
     *
     * @return mixed The filtered variable value, FALSE if filtering fails, or NULL if the variable is not set.
     *               If FILTER_NULL_ON_FAILURE is used, it returns FALSE if the variable is missing and NULL if filtering fails.
     */
    public static function filterInput(
        int $type,
        string $variableName,
        int $filter = FILTER_DEFAULT,
        int|array|null $options = null
    ) {
        // Retrieve input value, handling cases where options may be omitted.
        $var = ($options === null || func_num_args() < 4)
            ? filter_input($type, $variableName, $filter)
            : filter_input($type, $variableName, $filter, $options);

        // Apply additional filtering and normalization.
        return self::filter($var);
    }

    /**
     * A wrapper for `filter_input_array()` that normalizes input to UTF-8 NFC,
     * converting from WINDOWS-1252 when needed.
     *
     * Retrieves multiple external variables and applies optional filtering.
     *
     * EXAMPLE:
     * ```php
     * // Assuming $_GET['foo'] = 'bar';
     * UTF8::filterInputArray(INPUT_GET, ['foo' => FILTER_UNSAFE_RAW]); // Returns ['foo' => 'bar']
     * ```
     *
     * @see https://www.php.net/manual/en/function.filter-input-array.php
     *
     * @param int                       $type       One of INPUT_GET, INPUT_POST, INPUT_COOKIE, INPUT_SERVER, or INPUT_ENV.
     * @param array<string, mixed>|null $definition [optional] An array defining the filtering rules for each input.
     *                                              If set to an integer filter constant, all values will be filtered using that filter.
     * @param bool                      $addEmpty   [optional] Whether to include missing keys as NULL in the result.
     *
     * @return array<string, mixed>|false|null An array of filtered values, FALSE on failure, or NULL for missing variables.
     */
    public static function filterInputArray(
        int $type,
        array|null $definition = null,
        bool $addEmpty = true
    ): array|false|null {
        // Retrieve input array, handling cases where definition is omitted.
        $inputArray = ($definition === null || func_num_args() < 2)
            ? filter_input_array($type)
            : filter_input_array($type, $definition, $addEmpty);

        // Apply additional filtering and normalization.
        return self::filter($inputArray);
    }

    /**
     * A wrapper for `filter_var()` that normalizes input to UTF-8 NFC,
     * converting from WINDOWS-1252 when needed.
     *
     * Filters a variable using a specified filter.
     *
     * EXAMPLE:
     * ```php
     * UTF8::filterVar('-ABC-中文空白-', FILTER_VALIDATE_URL); // Returns false
     * ```
     *
     * @see https://www.php.net/manual/en/function.filter-var.php
     *
     * @param float|int|string|null $variable Value to be filtered.
     * @param int                   $filter   [optional] The filter ID to apply (default: FILTER_DEFAULT).
     * @param int|array             $options  [optional] An associative array of options or a bitwise disjunction of flags.
     *
     * @return mixed The filtered value, or FALSE if filtering fails.
     */
    public static function filterVar(
        float|int|string|null $variable,
        int $filter = FILTER_DEFAULT,
        int|array $options = 0
    ): mixed {
        // Apply filter_var with the provided arguments.
        $filteredVariable = (func_num_args() < 3)
            ? filter_var($variable, $filter)
            : filter_var($variable, $filter, $options);

        // Normalize the result with UTF-8 filtering.
        return self::filter($filteredVariable);
    }

    /**
     * A wrapper for `filter_var_array()` that normalizes input to UTF-8 NFC,
     * converting from WINDOWS-1252 when needed.
     *
     * Filters multiple variables based on the provided filter definition.
     *
     * EXAMPLE:
     * ```php
     * $filters = [
     *     'name'  => ['filter' => FILTER_CALLBACK, 'options' => [UTF8::class, 'ucwords']],
     *     'age'   => ['filter' => FILTER_VALIDATE_INT, 'options' => ['min_range' => 1, 'max_range' => 120]],
     *     'email' => FILTER_VALIDATE_EMAIL,
     * ];
     *
     * $data = [
     *     'name'  => 'κόσμε',
     *     'age'   => '18',
     *     'email' => 'foo@bar.de'
     * ];
     *
     * UTF8::filterVarArray($data, $filters, true); // ['name' => 'Κόσμε', 'age' => 18, 'email' => 'foo@bar.de']
     * ```
     *
     * @see https://www.php.net/manual/en/function.filter-var-array.php
     *
     * @param array<string, mixed>     $data       An associative array containing the data to filter.
     * @param array<string, mixed>|int $definition [optional] Filter definition array or a filter constant.
     * @param bool                     $addEmpty   [optional] Whether to add missing keys as NULL (default: true).
     *
     * @return array<string, mixed>|false|null Filtered data on success, FALSE on failure.
     */
    public static function filterVarArray(
        array $data,
        array|int $definition = 0,
        bool $addEmpty = true
    ): array|false|null {
        // Apply filter_var_array with the provided arguments.
        $filteredData = (func_num_args() < 2)
            ? filter_var_array($data)
            : filter_var_array($data, $definition, $addEmpty);

        // Normalize the filtered data with UTF-8 filtering.
        return self::filter($filteredData);
    }

    /**
     * Returns the first `$n` characters of the given string.
     *
     * @param string      $str      The input string.
     * @param int<1, max> $n        Number of characters to retrieve from the start.
     * @param string      $encoding [optional] Character encoding for string operations (default: UTF-8).
     *
     * @return string The first `$n` characters of `$str`.
     */
    public static function firstChar(
        string $str,
        int $n = 1,
        string $encoding = self::UTF8
    ): string {
        if ($str === '' || $n <= 0) {
            return '';
        }

        return ($encoding === self::UTF8)
            ? (string) mb_substr($str, 0, $n)
            : (string) self::substr($str, 0, $n, $encoding);
    }

    /**
     * Check if the number of Unicode characters isn't greater than the specified integer.
     *
     * EXAMPLE: <code>UTF8::fits_inside('κόσμε', 6); // false</code>
     *
     * @param string $str      the original string to be checked
     * @param int    $box_size the size in number of chars to be checked against string
     *
     * @psalm-pure
     *
     * @return bool
     *              <p><strong>TRUE</strong> if string is less than or equal to $box_size, <strong>FALSE</strong> otherwise.</p>
     */
    public static function fits_inside(string $str, int $box_size): bool
    {
        return (int) self::strlen($str) <= $box_size;
    }

    /**
     * Try to fix simple broken UTF-8 strings.
     *
     * INFO: Take a look at "UTF8::fixUtf8()" if you need a more advanced fix for broken UTF-8 strings.
     *
     * EXAMPLE: <code>UTF8::fixSimpleUtf8('DÃ¼sseldorf'); // 'Düsseldorf'</code>
     *
     * If you received a UTF-8 string that was converted from Windows-1252 as if it was ISO-8859-1
     * (ignoring Windows-1252 chars from 80 to 9F), use this function to fix it.
     * See: http://en.wikipedia.org/wiki/Windows-1252
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function fixSimpleUtf8(string $str): string
    {
        if ($str === '') {
            return '';
        }

        // Static variables for caching keys and values for broken UTF-8 fixes
        static $utf8FixKeysCache = null;
        static $utf8FixValuesCache = null;

        // Initialize the cache only once
        if ($utf8FixKeysCache === null) {
            // Fetch the broken UTF-8 to UTF-8 mapping data if not already loaded
            if (self::$BROKEN_UTF8_FIX === null) {
                self::$BROKEN_UTF8_FIX = self::getData('utf8_fix');
            }

            // Populate the cache
            $utf8FixKeysCache = array_keys(self::$BROKEN_UTF8_FIX ?? []);
            $utf8FixValuesCache = self::$BROKEN_UTF8_FIX;
        }

        // Perform the replacement with cached keys and values
        return str_replace($utf8FixKeysCache, $utf8FixValuesCache, $str);
    }

    /**
     * Fix a double (or multiple) encoded UTF8 string.
     *
     * EXAMPLE: <code>UTF8::fix_utf8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration'); // 'Fédération'</code>
     *
     * @param string|string[] $str you can use a string or an array of strings
     *
     * @psalm-pure
     *
     * @return string|string[]
     *                         <p>Will return the fixed input-"array" or
     *                         the fixed input-"string".</p>
     *
     * @template TFixUtf8 as string|string[]
     * @phpstan-param TFixUtf8 $str
     * @phpstan-return TFixUtf8
     */
    public static function fix_utf8($str)
    {
        if (\is_array($str)) {
            foreach ($str as &$v) {
                $v = self::fix_utf8($v);
            }
            unset($v);

            /**
             * @psalm-suppress InvalidReturnStatement
             */
            return $str;
        }

        $str = (string) $str; /* @phpstan-ignore-line | TFixUtf8 is string here */
        $last = '';
        while ($last !== $str) {
            $last = $str;
            /**
             * @psalm-suppress PossiblyInvalidArgument
             */
            $str = self::toUtf8(
                self::utf8Decode($str, true)
            );
        }

        /**
         * @psalm-suppress InvalidReturnStatement
         */
        return $str;
    }

    /**
     * Get character of a specific character.
     *
     * EXAMPLE: <code>UTF8::getCharDirection('ا'); // 'RTL'</code>
     *
     * @param string $char
     *
     * @psalm-pure
     *
     * @return string
     *                <p>'RTL' or 'LTR'.</p>
     */
    public static function getCharDirection(string $char): string
    {
        if (self::$SUPPORT[self::FEATURE_INTLCHAR] === true) {
            $tmp_return = \IntlChar::charDirection($char);

            // from "IntlChar"-Class
            $char_direction = [
                'RTL' => [1, 13, 14, 15, 21],
                'LTR' => [0, 11, 12, 20],
            ];

            if (\in_array($tmp_return, $char_direction['LTR'], true)) {
                return 'LTR';
            }

            if (\in_array($tmp_return, $char_direction['RTL'], true)) {
                return 'RTL';
            }
        }

        $c = static::chrToDecimal($char);

        if (!($c >= 0x5be && $c <= 0x10b7f)) {
            return 'LTR';
        }

        if ($c <= 0x85e) {
            if ($c === 0x5be ||
                $c === 0x5c0 ||
                $c === 0x5c3 ||
                $c === 0x5c6 ||
                ($c >= 0x5d0 && $c <= 0x5ea) ||
                ($c >= 0x5f0 && $c <= 0x5f4) ||
                $c === 0x608 ||
                $c === 0x60b ||
                $c === 0x60d ||
                $c === 0x61b ||
                ($c >= 0x61e && $c <= 0x64a) ||
                ($c >= 0x66d && $c <= 0x66f) ||
                ($c >= 0x671 && $c <= 0x6d5) ||
                ($c >= 0x6e5 && $c <= 0x6e6) ||
                ($c >= 0x6ee && $c <= 0x6ef) ||
                ($c >= 0x6fa && $c <= 0x70d) ||
                $c === 0x710 ||
                ($c >= 0x712 && $c <= 0x72f) ||
                ($c >= 0x74d && $c <= 0x7a5) ||
                $c === 0x7b1 ||
                ($c >= 0x7c0 && $c <= 0x7ea) ||
                ($c >= 0x7f4 && $c <= 0x7f5) ||
                $c === 0x7fa ||
                ($c >= 0x800 && $c <= 0x815) ||
                $c === 0x81a ||
                $c === 0x824 ||
                $c === 0x828 ||
                ($c >= 0x830 && $c <= 0x83e) ||
                ($c >= 0x840 && $c <= 0x858) ||
                $c === 0x85e
            ) {
                return 'RTL';
            }
        } elseif ($c === 0x200f) {
            return 'RTL';
        } elseif ($c >= 0xfb1d) {
            if ($c === 0xfb1d ||
                ($c >= 0xfb1f && $c <= 0xfb28) ||
                ($c >= 0xfb2a && $c <= 0xfb36) ||
                ($c >= 0xfb38 && $c <= 0xfb3c) ||
                $c === 0xfb3e ||
                ($c >= 0xfb40 && $c <= 0xfb41) ||
                ($c >= 0xfb43 && $c <= 0xfb44) ||
                ($c >= 0xfb46 && $c <= 0xfbc1) ||
                ($c >= 0xfbd3 && $c <= 0xfd3d) ||
                ($c >= 0xfd50 && $c <= 0xfd8f) ||
                ($c >= 0xfd92 && $c <= 0xfdc7) ||
                ($c >= 0xfdf0 && $c <= 0xfdfc) ||
                ($c >= 0xfe70 && $c <= 0xfe74) ||
                ($c >= 0xfe76 && $c <= 0xfefc) ||
                ($c >= 0x10800 && $c <= 0x10805) ||
                $c === 0x10808 ||
                ($c >= 0x1080a && $c <= 0x10835) ||
                ($c >= 0x10837 && $c <= 0x10838) ||
                $c === 0x1083c ||
                ($c >= 0x1083f && $c <= 0x10855) ||
                ($c >= 0x10857 && $c <= 0x1085f) ||
                ($c >= 0x10900 && $c <= 0x1091b) ||
                ($c >= 0x10920 && $c <= 0x10939) ||
                $c === 0x1093f ||
                $c === 0x10a00 ||
                ($c >= 0x10a10 && $c <= 0x10a13) ||
                ($c >= 0x10a15 && $c <= 0x10a17) ||
                ($c >= 0x10a19 && $c <= 0x10a33) ||
                ($c >= 0x10a40 && $c <= 0x10a47) ||
                ($c >= 0x10a50 && $c <= 0x10a58) ||
                ($c >= 0x10a60 && $c <= 0x10a7f) ||
                ($c >= 0x10b00 && $c <= 0x10b35) ||
                ($c >= 0x10b40 && $c <= 0x10b55) ||
                ($c >= 0x10b58 && $c <= 0x10b72) ||
                ($c >= 0x10b78)
            ) {
                return 'RTL';
            }
        }

        return 'LTR';
    }

    /**
     * Check for php-support.
     *
     * @param string|null $key
     *
     * @psalm-pure
     *
     * @return mixed
     *               Return the full support-"array", if $key === null<br>
     *               return bool-value, if $key is used and available<br>
     *               otherwise return <strong>null</strong>
     */
    public static function getSupportInfo(?string $key = null)
    {
        if ($key === null) {
            return self::$SUPPORT;
        }

        if (self::$INTL_TRANSLITERATOR_LIST === null) {
            self::$INTL_TRANSLITERATOR_LIST = self::getData('transliterator_list');
        }
        // compatibility fix for old versions
        self::$SUPPORT['intl__transliterator_list_ids'] = self::$INTL_TRANSLITERATOR_LIST;

        return self::$SUPPORT[$key] ?? null;
    }

    /**
     * Warning: this method only works for some file types (PNG, JPG).
     *          If you need more supported types, please use e.g. "finfo".
     *
     * @param string $str The string representing the file.
     * @param array{ext: null|string, mime: null|string, type: null|string} $fallback Default values for the file type.
     *
     * @return array{ext: null|string, mime: null|string, type: null|string}
     *
     * @psalm-pure
     */
    public static function getFileType(string $str, array $fallback = [
        'ext'  => null,
        'mime' => 'application/octet-stream',
        'type' => null,
    ]): array {
        if ($str === '') {
            return $fallback;
        }

        // Get the first two characters of the string and unpack.
        $strInfo = substr($str, 0, 2);
        if ($strInfo === false || strlen($strInfo) !== 2) {
            return $fallback;
        }

        $strInfo = unpack('C2chars', $strInfo);
        if ($strInfo === false) {
            return $fallback;
        }

        // Combine the two characters into a single integer code.
        $typeCode = (int) ($strInfo['chars1'] . $strInfo['chars2']);

        // Switch case based on the type code for supported file formats.
        switch ($typeCode) {
            case 255216: // JPG
                return [
                    'ext'  => 'jpg',
                    'mime' => 'image/jpeg',
                    'type' => 'binary',
                ];
            case 13780: // PNG
                return [
                    'ext'  => 'png',
                    'mime' => 'image/png',
                    'type' => 'binary',
                ];
            default:
                return $fallback;
        }
    }


    /**
     * @param int<1, max> $length         <p>Length of the random string.</p>
     * @param string      $possible_chars [optional] <p>Characters string for the random selection.</p>
     * @param string      $encoding       [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $possible_chars
     * @phpstan-return (T is non-empty-string ? non-empty-string : '')
     */
    public static function get_random_string(
        int $length,
        string $possible_chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789',
        string $encoding = self::UTF8
    ): string {
        // init
        $i = 0;
        $str = '';

        //
        // add random chars
        //

        if ($encoding === self::UTF8) {
            $max_length = (int) \mb_strlen($possible_chars);
            if ($max_length === 0) {
                return '';
            }

            while ($i < $length) {
                try {
                    $rand_int = \random_int(0, $max_length - 1);
                } catch (\Exception $e) {
                    $rand_int = \mt_rand(0, $max_length - 1);
                }
                $char = \mb_substr($possible_chars, $rand_int, 1);
                /* @phpstan-ignore-next-line | "false" was at least the return type in the past, or? */
                if ($char !== false) {
                    $str .= $char;
                    ++$i;
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $max_length = (int) self::strlen($possible_chars, $encoding);
            if ($max_length === 0) {
                return '';
            }

            while ($i < $length) {
                try {
                    $rand_int = \random_int(0, $max_length - 1);
                } catch (\Exception $e) {
                    $rand_int = \mt_rand(0, $max_length - 1);
                }
                $char = self::substr($possible_chars, $rand_int, 1, $encoding);
                if ($char !== false) {
                    $str .= $char;
                    ++$i;
                }
            }
        }

        return $str;
    }

    /**
     * @param int|string $extra_entropy [optional] <p>Extra entropy via a string or int value.</p>
     * @param bool       $use_md5       [optional] <p>Return the unique identifier as md5-hash? Default: true</p>
     *
     * @return non-empty-string
     */
    public static function get_unique_string($extra_entropy = '', bool $use_md5 = true): string
    {
        try {
            $rand_int = \random_int(0, \mt_getrandmax());
        } catch (\Exception $e) {
            $rand_int = \mt_rand(0, \mt_getrandmax());
        }

        $unique_helper = $rand_int .
                         \session_id() .
                         ($_SERVER['REMOTE_ADDR'] ?? '') .
                         ($_SERVER['SERVER_ADDR'] ?? '') .
                         $extra_entropy;

        $unique_string = \uniqid($unique_helper, true);

        if ($use_md5) {
            $unique_string = \md5($unique_string . $unique_helper);
        }

        return $unique_string;
    }

    /**
     * Returns true if the string contains a lower case char, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not the string contains a lower case character.</p>
     */
    public static function has_lowercase(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('.*[[:lower:]]', $str);
        }

        return self::str_matches_pattern($str, '.*[[:lower:]]');
    }

    /**
     * Returns true if the string contains whitespace, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not the string contains whitespace.</p>
     */
    public static function has_whitespace(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('.*[[:space:]]', $str);
        }

        return self::str_matches_pattern($str, '.*[[:space:]]');
    }

    /**
     * Returns true if the string contains an upper case char, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not the string contains an upper case character.</p>
     */
    public static function has_uppercase(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('.*[[:upper:]]', $str);
        }

        return self::str_matches_pattern($str, '.*[[:upper:]]');
    }

    /**
     * Converts a hexadecimal value into a UTF-8 character.
     *
     * INFO: opposite to UTF8::chrToHex()
     *
     * EXAMPLE: <code>UTF8::hex_to_chr('U+00a7'); // '§'</code>
     *
     * @param string $hexdec <p>The hexadecimal value.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                      <p>One single UTF-8 character.</p>
     */
    public static function hex_to_chr(string $hexdec)
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection - Invalid characters passed for attempted conversion, these have been ignored */
        return self::decimalToChr((int) @\hexdec($hexdec));
    }

    /**
     * Converts hexadecimal U+xxxx code point representation to integer.
     *
     * INFO: opposite to UTF8::intToHex()
     *
     * EXAMPLE: <code>UTF8::hex_to_int('U+00f1'); // 241</code>
     *
     * @param string $hexdec <p>The hexadecimal code point representation.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The code point, or false on failure.</p>
     */
    public static function hex_to_int($hexdec)
    {
        // init
        $hexdec = (string) $hexdec;

        if ($hexdec === '') {
            return false;
        }

        if (\preg_match('/^(?:\\\u|U\+|)([a-zA-Z0-9]{4,6})$/', $hexdec, $match)) {
            return \intval($match[1], 16);
        }

        return false;
    }

    /**
     * Converts a UTF-8 string to a series of HTML numbered entities.
     *
     * INFO: Opposite to UTF8::htmlDecode()
     *
     * EXAMPLE: <code>UTF8::htmlEncode('中文空白'); // '&#20013;&#25991;&#31354;&#30333;'</code>
     *
     * @param string $str              The Unicode string to be encoded as numbered entities.
     * @param bool   $keepAsciiChars   [optional] Whether to keep ASCII characters.
     * @param string $encoding         [optional] Character set for encoding functions.
     *
     * @psalm-pure
     *
     * @return string HTML numbered entities.
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function htmlEncode(
        string $str,
        bool $keepAsciiChars = false,
        string $encoding = self::UTF8
    ): string {
        if ($str === '') {
            return '';
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }

        // INFO: Explanation of convmap: https://stackoverflow.com/questions/35854535/
        if (self::$SUPPORT[self::FEATURE_MBSTRING]) {
            $startCode = $keepAsciiChars ? 0x80 : 0x00;

            $encoded = mb_encode_numericentity($str, [$startCode, 0xFFFFF, 0, 0xFFFFF], $encoding);
            if ($encoded !== false) {
                return $encoded;
            }
        }

        // Fallback using standard PHP functions
        return implode('', array_map(
            static fn(string $chr): string => self::singleChrHtmlEncode($chr, $keepAsciiChars, $encoding),
            self::strSplit($str)
        ));
    }

    /**
     * UTF-8 version of htmlEntityDecode()
     *
     * The reason we are not using htmlEntityDecode() by itself is because
     * while it is not technically correct to leave out the semicolon
     * at the end of an entity most browsers will still interpret the entity
     * correctly. htmlEntityDecode() does not convert entities without
     * semicolons, so we are left with our own little solution here. Bummer.
     *
     * Convert all HTML entities to their applicable characters.
     *
     * INFO: opposite to UTF8::htmlEncode()
     *
     * EXAMPLE: <code>UTF8htmlEntityDecode('&#20013;&#25991;&#31354;&#30333;'); // '中文空白'</code>
     *
     * @see http://php.net/manual/en/function.html-entity-decode.php
     *
     * @param string   $str      <p>
     *                           The input string.
     *                           </p>
     * @param int|null $flags    [optional] <p>
     *                           A bitmask of one or more of the following flags, which specify how to handle quotes
     *                           and which document type to use. The default is ENT_COMPAT | ENT_HTML401.
     *                           <table>
     *                           Available <i>flags</i> constants
     *                           <tr valign="top">
     *                           <td>Constant Name</td>
     *                           <td>Description</td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_COMPAT</b></td>
     *                           <td>Will convert double-quotes and leave single-quotes alone.</td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_QUOTES</b></td>
     *                           <td>Will convert both double and single quotes.</td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_NOQUOTES</b></td>
     *                           <td>Will leave both double and single quotes unconverted.</td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_HTML401</b></td>
     *                           <td>
     *                           Handle code as HTML 4.01.
     *                           </td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_XML1</b></td>
     *                           <td>
     *                           Handle code as XML 1.
     *                           </td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_XHTML</b></td>
     *                           <td>
     *                           Handle code as XHTML.
     *                           </td>
     *                           </tr>
     *                           <tr valign="top">
     *                           <td><b>ENT_HTML5</b></td>
     *                           <td>
     *                           Handle code as HTML 5.
     *                           </td>
     *                           </tr>
     *                           </table>
     *                           </p>
     * @param string   $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string The decoded string.
     */
    public static function htmlEntityDecode(
        string $str,
        ?int $flags = null,
        string $encoding = self::UTF8
    ): string {
        if (!isset($str[3]) || strpos($str, '&') === false) {
            return $str;
        }
    
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }
    
        $flags ??= ENT_QUOTES | ENT_HTML5;
    
        if (!in_array($encoding, [self::UTF8, self::ISO88591, self::WINDOWS1252], true) && !self::$SUPPORT[self::FEATURE_MBSTRING]) {
            trigger_error('UTF8::htmlEntityDecode() without mbstring cannot handle "' . $encoding . '" encoding', E_USER_WARNING);
        }
    
        do {
            $previous = $str;
    
            if (strpos($str, '&') !== false) {
                if (strpos($str, '&#') !== false) {
                    $str = preg_replace(
                        '/(&#(?:x0*[0-9a-fA-F]{2,6}(?![0-9a-fA-F;])|(?:0*\d{2,6}(?![0-9;]))))/S',
                        '$1;',
                        $str
                    ) ?? $str;
                }
                $str = html_entity_decode($str, $flags, $encoding);
            }
        } while ($previous !== $str);
    
        return $str;
    }
    

    /**
     * Create a escape html version of the string via "UTF8::htmlspecialchars()".
     *
     * @param string $str
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function html_escape(string $str, string $encoding = self::UTF8): string
    {
        return self::htmlspecialchars(
            $str,
            \ENT_QUOTES | \ENT_SUBSTITUTE,
            $encoding
        );
    }

    /**
     * Remove empty html-tag.
     *
     * e.g.: <pre><tag></tag></pre>
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function html_stripe_empty_tags(string $str): string
    {
        return (string) \preg_replace(
            '/<[^\\/>]*?>\\s*?<\\/[^>]*?>/u',
            '',
            $str
        );
    }

    /**
     * Convert all applicable characters to HTML entities: UTF-8 version of htmlentities().
     *
     * EXAMPLE: <code>UTF8::htmlentities('<白-öäü>'); // '&lt;&#30333;-&ouml;&auml;&uuml;&gt;'</code>
     *
     * @see http://php.net/manual/en/function.htmlentities.php
     *
     * @param string $str           <p>
     *                              The input string.
     *                              </p>
     * @param int    $flags         [optional] <p>
     *                              A bitmask of one or more of the following flags, which specify how to handle
     *                              quotes, invalid code unit sequences and the used document type. The default is
     *                              ENT_COMPAT | ENT_HTML401.
     *                              <table>
     *                              Available <i>flags</i> constants
     *                              <tr valign="top">
     *                              <td>Constant Name</td>
     *                              <td>Description</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_COMPAT</b></td>
     *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_QUOTES</b></td>
     *                              <td>Will convert both double and single quotes.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_NOQUOTES</b></td>
     *                              <td>Will leave both double and single quotes unconverted.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_IGNORE</b></td>
     *                              <td>
     *                              Silently discard invalid code unit sequences instead of returning
     *                              an empty string. Using this flag is discouraged as it
     *                              may have security implications.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_SUBSTITUTE</b></td>
     *                              <td>
     *                              Replace invalid code unit sequences with a Unicode Replacement Character
     *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
     *                              string.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_DISALLOWED</b></td>
     *                              <td>
     *                              Replace invalid code points for the given document type with a
     *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
     *                              (otherwise) instead of leaving them as is. This may be useful, for
     *                              instance, to ensure the well-formedness of XML documents with
     *                              embedded external content.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_HTML401</b></td>
     *                              <td>
     *                              Handle code as HTML 4.01.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_XML1</b></td>
     *                              <td>
     *                              Handle code as XML 1.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_XHTML</b></td>
     *                              <td>
     *                              Handle code as XHTML.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_HTML5</b></td>
     *                              <td>
     *                              Handle code as HTML 5.
     *                              </td>
     *                              </tr>
     *                              </table>
     *                              </p>
     * @param string $encoding      [optional] <p>
     *                              Like <b>htmlspecialchars</b>,
     *                              <b>htmlentities</b> takes an optional third argument
     *                              <i>encoding</i> which defines encoding used in
     *                              conversion.
     *                              Although this argument is technically optional, you are highly
     *                              encouraged to specify the correct value for your code.
     *                              </p>
     * @param bool   $double_encode [optional] <p>
     *                              When <i>double_encode</i> is turned off PHP will not
     *                              encode existing html entities. The default is to convert everything.
     *                              </p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>
     *                The encoded string.
     *                <br><br>
     *                If the input <i>string</i> contains an invalid code unit
     *                sequence within the given <i>encoding</i> an empty string
     *                will be returned, unless either the <b>ENT_IGNORE</b> or
     *                <b>ENT_SUBSTITUTE</b> flags are set.
     *                </p>
     */
    public static function htmlentities(
        string $str,
        int $flags = \ENT_COMPAT,
        string $encoding = self::UTF8,
        bool $double_encode = true
    ): string {
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        $str = \htmlentities(
            $str,
            $flags,
            $encoding,
            $double_encode
        );

        /**
         * PHP doesn't replace a backslash to its html entity since this is something
         * that's mostly used to escape characters when inserting in a database. Since
         * we're using a decent database layer, we don't need this shit and we're replacing
         * the double backslashes by its' html entity equivalent.
         *
         * https://github.com/forkcms/library/blob/master/spoon/filter/filter.php#L303
         */
        $str = \str_replace('\\', '&#92;', $str);

        return self::htmlEncode($str, true, $encoding);
    }

    /**
     * Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()
     *
     * INFO: Take a look at "UTF8::htmlentities()"
     *
     * EXAMPLE: <code>UTF8::htmlspecialchars('<白-öäü>'); // '&lt;白-öäü&gt;'</code>
     *
     * @see http://php.net/manual/en/function.htmlspecialchars.php
     *
     * @param string $str           <p>
     *                              The string being converted.
     *                              </p>
     * @param int    $flags         [optional] <p>
     *                              A bitmask of one or more of the following flags, which specify how to handle
     *                              quotes, invalid code unit sequences and the used document type. The default is
     *                              ENT_COMPAT | ENT_HTML401.
     *                              <table>
     *                              Available <i>flags</i> constants
     *                              <tr valign="top">
     *                              <td>Constant Name</td>
     *                              <td>Description</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_COMPAT</b></td>
     *                              <td>Will convert double-quotes and leave single-quotes alone.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_QUOTES</b></td>
     *                              <td>Will convert both double and single quotes.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_NOQUOTES</b></td>
     *                              <td>Will leave both double and single quotes unconverted.</td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_IGNORE</b></td>
     *                              <td>
     *                              Silently discard invalid code unit sequences instead of returning
     *                              an empty string. Using this flag is discouraged as it
     *                              may have security implications.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_SUBSTITUTE</b></td>
     *                              <td>
     *                              Replace invalid code unit sequences with a Unicode Replacement Character
     *                              U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
     *                              string.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_DISALLOWED</b></td>
     *                              <td>
     *                              Replace invalid code points for the given document type with a
     *                              Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
     *                              (otherwise) instead of leaving them as is. This may be useful, for
     *                              instance, to ensure the well-formedness of XML documents with
     *                              embedded external content.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_HTML401</b></td>
     *                              <td>
     *                              Handle code as HTML 4.01.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_XML1</b></td>
     *                              <td>
     *                              Handle code as XML 1.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_XHTML</b></td>
     *                              <td>
     *                              Handle code as XHTML.
     *                              </td>
     *                              </tr>
     *                              <tr valign="top">
     *                              <td><b>ENT_HTML5</b></td>
     *                              <td>
     *                              Handle code as HTML 5.
     *                              </td>
     *                              </tr>
     *                              </table>
     *                              </p>
     * @param string $encoding      [optional] <p>
     *                              Defines encoding used in conversion.
     *                              </p>
     *                              <p>
     *                              For the purposes of this function, the encodings
     *                              ISO-8859-1, ISO-8859-15,
     *                              UTF-8, cp866,
     *                              cp1251, cp1252, and
     *                              KOI8-R are effectively equivalent, provided the
     *                              <i>string</i> itself is valid for the encoding, as
     *                              the characters affected by <b>htmlspecialchars</b> occupy
     *                              the same positions in all of these encodings.
     *                              </p>
     * @param bool   $double_encode [optional] <p>
     *                              When <i>double_encode</i> is turned off PHP will not
     *                              encode existing html entities, the default is to convert everything.
     *                              </p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The converted string.</p>
     *                <p>
     *                If the input <i>string</i> contains an invalid code unit
     *                sequence within the given <i>encoding</i> an empty string
     *                will be returned, unless either the <b>ENT_IGNORE</b> or
     *                <b>ENT_SUBSTITUTE</b> flags are set.</p>
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function htmlspecialchars(
        string $str,
        int $flags = \ENT_COMPAT,
        string $encoding = self::UTF8,
        bool $double_encode = true
    ): string {
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        return \htmlspecialchars(
            $str,
            $flags,
            $encoding,
            $double_encode
        );
    }

    /**
     * Converts an integer to a hexadecimal U+xxxx code point representation.
     *
     * Example: UTF8::intToHex(241); // 'U+00F1'
     *
     * @param int $int The integer to be converted to a hexadecimal code point.
     * @param string $prefix [optional] The prefix for the output format. Default: 'U+'.
     *
     * @psalm-pure
     *
     * @return string The code point, or an empty string on failure.
     */
    public static function intToHex(int $int, string $prefix = 'U+'): string
    {
        $hex = str_pad(dechex($int), 4, '0', STR_PAD_LEFT);
        return $prefix . strtoupper($hex);
    }

    /**
     * Returns true if the string contains only alphabetic chars, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only alphabetic chars.</p>
     */
    public static function is_alpha(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:alpha:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:alpha:]]*$');
    }

    /**
     * Returns true if the string contains only alphabetic and numeric chars, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only alphanumeric chars.</p>
     */
    public static function is_alphanumeric(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:alnum:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:alnum:]]*$');
    }

    /**
     * Returns true if the string contains only punctuation chars, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only punctuation chars.</p>
     */
    public static function is_punctuation(string $str): bool
    {
        return self::str_matches_pattern($str, '^[[:punct:]]*$');
    }

    /**
     * Returns true if the string contains only printable (non-invisible) chars, false otherwise.
     *
     * @param string $str                       <p>The input string.</p>
     * @param bool   $ignore_control_characters [optional] <p>Ignore control characters like [LRM] or [LSEP].</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only printable (non-invisible) chars.</p>
     */
    public static function is_printable(string $str, bool $ignore_control_characters = false): bool
    {
        return self::removeInvisibleCharacters($str, false, '', $ignore_control_characters) === $str;
    }

    /**
     * Checks if a string is 7 bit ASCII.
     *
     * EXAMPLE: <code>UTF8::isAscii('白'); // false</code>
     *
     * @param string $str <p>The string to check.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>
     *              <strong>true</strong> if it is ASCII<br>
     *              <strong>false</strong> otherwise
     *              </p>
     */
    public static function isAscii(string $str): bool
    {
        return ASCII::isAscii($str);
    }

    /**
     * Returns true if the string is base64 encoded, false otherwise.
     *
     * EXAMPLE: <code>UTF8::is_base64('4KSu4KWL4KSo4KS/4KSa'); // true</code>
     *
     * @param string|null $str                   <p>The input string.</p>
     * @param bool        $empty_string_is_valid [optional] <p>Is an empty string valid base64 or not?</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str is base64 encoded.</p>
     */
    public static function is_base64($str, bool $empty_string_is_valid = false): bool
    {
        if (
            !$empty_string_is_valid
            &&
            $str === ''
        ) {
            return false;
        }

        if (!\is_string($str)) {
            return false;
        }

        $base64String = \base64_decode($str, true);

        return $base64String !== false && \base64_encode($base64String) === $str;
    }

    /**
     * Check if the input is binary (looks like a hack).
     *
     * EXAMPLE: <code>UTF8::isBinary('01'); // true</code>
     *
     * @param int|string $input
     * @param bool       $strict
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function isBinary($input, bool $strict = false): bool
    {
        $input = (string) $input;
        if ($input === '') {
            return false;
        }

        // Check if the string consists of only binary digits (0 and 1).
        if (preg_match('~^[01]+$~', $input)) {
            return true;
        }

        // Check file type if not strictly checking.
        $fileType = self::getFileType($input);
        if ($fileType['type'] === 'binary') {
            return true;
        }

        // Perform a heuristic check if strict mode is off.
        if (!$strict) {
            $testLength = strlen($input);
            $testNullCount = substr_count($input, "\x0");
            if (($testNullCount / $testLength) > 0.25) {
                return true;
            }
        }

        // Perform stricter binary check if 'strict' is true.
        if ($strict) {
            if (!self::$SUPPORT[self::FEATURE_FINFO]) {
                throw new RuntimeException('ext-fileinfo: is not installed');
            }

            $finfo = new finfo(FILEINFO_MIME_ENCODING);
            $finfoEncoding = $finfo->buffer($input);
            if ($finfoEncoding && $finfoEncoding === 'binary') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the file is binary.
     *
     * EXAMPLE: <code>UTF8::isBinary('./utf32.txt'); // true</code>
     *
     * @param string $file
     *
     * @return bool
     */
    public static function is_binary_file($file): bool
    {
        // init
        $block = '';

        $fp = \fopen($file, 'rb');
        if (\is_resource($fp)) {
            $block = \fread($fp, 512);
            \fclose($fp);
        }

        if ($block === '' || $block === false) {
            return false;
        }

        return self::isBinary($block, true);
    }

    /**
     * Returns true if the string contains only whitespace chars, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only whitespace characters.</p>
     */
    public static function is_blank(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:space:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:space:]]*$');
    }

    /**
     * Checks if the given string is equal to any "Byte Order Mark".
     *
     * WARNING: Use "UTF8::hasBom()" if you will check BOM in a string.
     *
     * EXAMPLE: <code>UTF8::is_bom("\xef\xbb\xbf"); // true</code>
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p><strong>true</strong> if the $utf8_chr is Byte Order Mark, <strong>false</strong> otherwise.</p>
     */
    public static function is_bom($str): bool
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        foreach (self::$BOM as $bom_string => &$bom_byte_length) {
            if ($str === $bom_string) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the string is considered to be empty.
     *
     * A variable is considered empty if it does not exist or if its value equals FALSE.
     * empty() does not generate a warning if the variable does not exist.
     *
     * @param array<array-key, mixed>|float|int|string $str
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str is empty().</p>
     */
    public static function is_empty($str): bool
    {
        return empty($str);
    }

    /**
     * Returns true if the string contains only hexadecimal chars, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only hexadecimal chars.</p>
     */
    public static function is_hexadecimal(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:xdigit:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:xdigit:]]*$');
    }

    /**
     * Check if the string contains any HTML tags.
     *
     * EXAMPLE: <code>UTF8::is_html('<b>lall</b>'); // true</code>
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains html elements.</p>
     */
    public static function is_html(string $str): bool
    {
        if ($str === '') {
            return false;
        }

        // init
        $matches = [];

        $str = self::emojiEncode($str); // hack for emoji support :/

        \preg_match("/<\\/?\\w+(?:(?:\\s+\\w+(?:\\s*=\\s*(?:\".*?\"|'.*?'|[^'\">\\s]+))?)*\\s*|\\s*)\\/?>/u", $str, $matches);

        return $matches !== [];
    }

    /**
     * Check if $url is an correct url.
     *
     * @param string $url
     * @param bool   $disallow_localhost
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function is_url(string $url, bool $disallow_localhost = false): bool
    {
        if ($url === '') {
            return false;
        }

        // WARNING: keep this as hack protection
        if (!self::str_istarts_with_any($url, ['http://', 'https://'])) {
            return false;
        }

        // e.g. -> the server itself connect to "https://foo.localhost/phpmyadmin/...
        if ($disallow_localhost) {
            if (self::str_istarts_with_any(
                $url,
                [
                    'http://localhost',
                    'https://localhost',
                    'http://127.0.0.1',
                    'https://127.0.0.1',
                    'http://::1',
                    'https://::1',
                ]
            )) {
                return false;
            }

            $regex = '/^(?:http(?:s)?:\/\/).*?(?:\.localhost)/iu';
            if (\preg_match($regex, $url)) {
                return false;
            }
        }

        // INFO: this is needed for e.g. "http://müller.de/" (internationalized domain names) and non ASCII-parameters
        $regex = '/^(?:http(?:s)?:\\/\\/)(?:[\p{L}0-9][\p{L}0-9_-]*(?:\\.[\p{L}0-9][\p{L}0-9_-]*))(?:\\d+)?(?:\\/\\.*)?/iu';
        if (\preg_match($regex, $url)) {
            return true;
        }

        return \filter_var($url, \FILTER_VALIDATE_URL) !== false;
    }

    /**
     * Try to check if "$str" is a JSON-string.
     *
     * EXAMPLE: <code>UTF8::is_json('{"array":[1,"¥","ä"]}'); // true</code>
     *
     * @param string $str                                    <p>The input string.</p>
     * @param bool   $only_array_or_object_results_are_valid [optional] <p>Only array and objects are valid json
     *                                                       results.</p>
     *
     * @return bool
     *              <p>Whether or not the $str is in JSON format.</p>
     */
    public static function is_json(string $str, bool $only_array_or_object_results_are_valid = true): bool
    {
        if ($str === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_JSON] === false) {
            throw new \RuntimeException('ext-json: is not installed');
        }

        $jsonOrNull = self::jsonDecode($str);
        if ($jsonOrNull === null && \strtoupper($str) !== 'NULL') {
            return false;
        }

        if (
            $only_array_or_object_results_are_valid
            &&
            !\is_object($jsonOrNull)
            &&
            !\is_array($jsonOrNull)
        ) {
            return false;
        }

        return \json_last_error() === \JSON_ERROR_NONE;
    }

    /**
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only lowercase chars.</p>
     */
    public static function is_lowercase(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:lower:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:lower:]]*$');
    }

    /**
     * Returns true if the string is serialized, false otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str is serialized.</p>
     */
    public static function is_serialized(string $str): bool
    {
        if ($str === '') {
            return false;
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        /** @noinspection UnserializeExploitsInspection */
        return $str === 'b:0;'
               ||
               @\unserialize($str, []) !== false;
    }

    /**
     * Returns true if the string contains only lower case chars, false
     * otherwise.
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains only lower case characters.</p>
     */
    public static function is_uppercase(string $str): bool
    {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            return \mb_ereg_match('^[[:upper:]]*$', $str);
        }

        return self::str_matches_pattern($str, '^[[:upper:]]*$');
    }

    /**
     * Check if the string is UTF-16.
     *
     * EXAMPLE: <code>
     * UTF8::isUtf16(file_get_contents('utf-16-le.txt')); // 1
     * //
     * UTF8::isUtf16(file_get_contents('utf-16-be.txt')); // 2
     * //
     * UTF8::isUtf16(file_get_contents('utf-8.txt')); // false
     * </code>
     *
     * @param string $str The input string.
     * @param bool   $checkIfStringIsBinary Check if the string is binary.
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <strong>false</strong> if it is not UTF-16,<br>
     *                   <strong>1</strong> for UTF-16LE,<br>
     *                   <strong>2</strong> for UTF-16BE
     */
    public static function isUtf16(string $str, bool $checkIfStringIsBinary = true)
    {
        $str = (string) $str;
        $strChars = [];

        // Check for BOM presence and adjust the binary check
        if ($checkIfStringIsBinary !== false && self::hasBom($str)) {
            $checkIfStringIsBinary = false;
        }

        if ($checkIfStringIsBinary && !self::isBinary($str, true)) {
            return false;
        }

        // Trigger warning if mbstring is unavailable
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === false) {
            trigger_error('UTF8::isUtf16() without mbstring may not work correctly', E_USER_WARNING);
        }

        // Remove BOM if present
        $str = self::removeBom($str);

        // Check for UTF-16LE encoding
        $maybeUtf16Le = 0;
        $test = mb_convert_encoding($str, self::UTF8, self::UTF16LE);
        if ($test) {
            $test2 = mb_convert_encoding($test, self::UTF16LE, self::UTF8);
            $test3 = mb_convert_encoding($test2, self::UTF8, self::UTF16LE);
            if ($test3 === $test) {
                $strChars = self::countChars($str, true, false);
                foreach (self::countChars($test3) as $test3Char => $empty) {
                    if (in_array($test3Char, $strChars, true)) {
                        ++$maybeUtf16Le;
                    }
                }
            }
        }

        // Check for UTF-16BE encoding
        $maybeUtf16Be = 0;
        $test = mb_convert_encoding($str, self::UTF8, self::UTF16BE);
        if ($test) {
            $test2 = mb_convert_encoding($test, self::UTF16BE, self::UTF8);
            $test3 = mb_convert_encoding($test2, self::UTF8, self::UTF16BE);
            if ($test3 === $test) {
                if (empty($strChars)) {
                    $strChars = self::countChars($str, true, false);
                }
                foreach (self::countChars($test3) as $test3Char => $empty) {
                    if (in_array($test3Char, $strChars, true)) {
                        ++$maybeUtf16Be;
                    }
                }
            }
        }

        // Return the result based on the comparison of UTF-16LE and UTF-16BE counts
        if ($maybeUtf16Be !== $maybeUtf16Le) {
            return $maybeUtf16Le > $maybeUtf16Be ? 1 : 2;
        }

        return false;
    }

    /**
     * Check if the string is UTF-32.
     *
     * EXAMPLE: <code>
     * UTF8::isUtf32(file_get_contents('utf-32-le.txt')); // 1
     * //
     * UTF8::isUtf32(file_get_contents('utf-32-be.txt')); // 2
     * //
     * UTF8::isUtf32(file_get_contents('utf-8.txt')); // false
     * </code>
     *
     * @param string $str The input string.
     * @param bool   $checkIfStringIsBinary Check if the string is binary.
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <strong>false</strong> if it is not UTF-32,<br>
     *                   <strong>1</strong> for UTF-32LE,<br>
     *                   <strong>2</strong> for UTF-32BE
     */
    public static function isUtf32(string $str, bool $checkIfStringIsBinary = true)
    {
        $str = (string) $str;
        $strChars = [];

        // Check if binary string is valid and not a BOM-encoded string
        if ($checkIfStringIsBinary !== false && self::hasBom($str)) {
            $checkIfStringIsBinary = false;
        }

        if ($checkIfStringIsBinary && !self::isBinary($str, true)) {
            return false;
        }

        // Trigger warning if mbstring is not available
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === false) {
            trigger_error('UTF8::isUtf32() without mbstring may not work correctly', E_USER_WARNING);
        }

        // Remove BOM if present
        $str = self::removeBom($str);

        // Check for UTF-32LE encoding
        $maybeUtf32Le = 0;
        $test = mb_convert_encoding($str, self::UTF8, self::UTF32LE);
        if ($test) {
            $test2 = mb_convert_encoding($test, self::UTF32LE, self::UTF8);
            $test3 = mb_convert_encoding($test2, self::UTF8, self::UTF32LE);
            if ($test3 === $test) {
                $strChars = self::countChars($str, true, false);
                foreach (self::countChars($test3) as $test3Char => $empty) {
                    if (in_array($test3Char, $strChars, true)) {
                        ++$maybeUtf32Le;
                    }
                }
            }
        }

        // Check for UTF-32BE encoding
        $maybeUtf32Be = 0;
        $test = mb_convert_encoding($str, self::UTF8, self::UTF32BE);
        if ($test) {
            $test2 = mb_convert_encoding($test, self::UTF32BE, self::UTF8);
            $test3 = mb_convert_encoding($test2, self::UTF8, self::UTF32BE);
            if ($test3 === $test) {
                if (empty($strChars)) {
                    $strChars = self::countChars($str, true, false);
                }
                foreach (self::countChars($test3) as $test3Char => $empty) {
                    if (in_array($test3Char, $strChars, true)) {
                        ++$maybeUtf32Be;
                    }
                }
            }
        }

        // Return the result based on comparison of UTF-32LE and UTF-32BE counts
        if ($maybeUtf32Be !== $maybeUtf32Le) {
            return $maybeUtf32Le > $maybeUtf32Be ? 1 : 2;
        }

        return false;
    }

    /**
     * Checks whether the passed input contains only byte sequences that appear valid UTF-8.
     *
     * EXAMPLE: <code>
     * UTF8::is_utf8(['Iñtërnâtiônàlizætiøn', 'foo']); // true
     * //
     * UTF8::is_utf8(["Iñtërnâtiônàlizætiøn\xA0\xA1", 'bar']); // false
     * </code>
     *
     * @param int|string|string[]|null $str    <p>The input to be checked.</p>
     * @param bool                     $strict <p>Check also if the string is not UTF-16 or UTF-32.</p>
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function is_utf8($str, bool $strict = false): bool
    {
        if (\is_array($str)) {
            foreach ($str as &$v) {
                if (!self::is_utf8($v, $strict)) {
                    return false;
                }
            }

            return true;
        }

        return self::isUtf8String((string) $str, $strict);
    }

    /**
     * (PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
     * Decodes a JSON string
     *
     * EXAMPLE: <code>UTF8::jsonDecode('[1,"\u00a5","\u00e4"]'); // array(1, '¥', 'ä')</code>
     *
     * @see http://php.net/manual/en/function.json-decode.php
     *
     * @param string $json    <p>
     *                        The <i>json</i> string being decoded.
     *                        </p>
     *                        <p>
     *                        This function only works with UTF-8 encoded strings.
     *                        </p>
     *                        <p>PHP implements a superset of
     *                        JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
     *                        only supports these values when they are nested inside an array or an object.
     *                        </p>
     * @param bool   $assoc   [optional] <p>
     *                        When <b>TRUE</b>, returned objects will be converted into
     *                        associative arrays.
     *                        </p>
     * @param int    $depth   [optional] <p>
     *                        User specified recursion depth.
     *                        </p>
     * @param int    $options [optional] <p>
     *                        Bitmask of JSON decode options. Currently only
     *                        <b>JSON_BIGINT_AS_STRING</b>
     *                        is supported (default is to cast large integers as floats)
     *                        </p>
     *
     * @psalm-pure
     *
     * @return mixed
     *               <p>The value encoded in <i>json</i> in appropriate PHP type. Values true, false and
     *               null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b> and <b>NULL</b> respectively.
     *               <b>NULL</b> is returned if the <i>json</i> cannot be decoded or if the encoded data
     *               is deeper than the recursion limit.</p>
     */
    public static function jsonDecode(
        string $json,
        bool $assoc = false,
        int $depth = 512,
        int $options = 0
    ) {
        $json = self::filter($json);
    
        if (!self::$SUPPORT[self::FEATURE_JSON]) {
            throw new RuntimeException('ext-json is not installed');
        }
    
        return json_decode($json, $assoc, max($depth, 1), $options);
    }    

    /**
     * (PHP 5 >= 5.2.0, PECL json >= 1.2.0)
     * Returns the JSON representation of a value.
     *
     * EXAMPLE: <code>UTF8::jsonEncode([1, '¥', 'ä']); // '[1,"\u00a5","\u00e4"]'</code>
     *
     * @see http://php.net/manual/en/function.json-encode.php
     *
     * @param mixed $value   The value being encoded. Can be any type except a resource.
     *                       All string data must be UTF-8 encoded.
     *                       PHP implements a superset of JSON - it will also encode and decode 
     *                       scalar types and NULL. The JSON standard only supports these values 
     *                       when nested inside an array or an object.
     * @param int   $options [optional] Bitmask of JSON encoding options such as:
     *                       JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, 
     *                       JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, 
     *                       JSON_FORCE_OBJECT, JSON_UNESCAPED_UNICODE.
     * @param int   $depth   [optional] Maximum depth, must be greater than zero.
     *
     * @psalm-pure
     *
     * @return false|string  A JSON encoded string on success or FALSE on failure.
     */
    public static function jsonEncode(mixed $value, int $options = 0, int $depth = 512): false|string
    {
        $value = self::filter($value);

        if (!self::$SUPPORT[self::FEATURE_JSON]) {
            throw new RuntimeException('ext-json: is not installed');
        }

        return json_encode($value, $options, max($depth, 1));
    }

    /**
     * Makes string's first char lowercase.
     *
     * EXAMPLE: <code>UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'); // ñTËRNÂTIÔNÀLIZÆTIØN</code>
     *
     * @param string      $str                           <p>The input string</p>
     * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The resulting string.</p>
     */
    public static function lcfirst(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if ($clean_utf8) {
            $str = self::clean($str);
        }

        $use_mb_functions = ($lang === null && !$try_to_keep_the_string_length);

        if ($encoding === self::UTF8) {
            $str_part_two = (string) \mb_substr($str, 1);

            if ($use_mb_functions) {
                $str_part_one = \mb_strtolower(
                    (string) \mb_substr($str, 0, 1)
                );
            } else {
                $str_part_one = self::strtolower(
                    (string) \mb_substr($str, 0, 1),
                    $encoding,
                    false,
                    $lang,
                    $try_to_keep_the_string_length
                );
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $str_part_two = (string) self::substr($str, 1, null, $encoding);

            $str_part_one = self::strtolower(
                (string) self::substr($str, 0, 1, $encoding),
                $encoding,
                false,
                $lang,
                $try_to_keep_the_string_length
            );
        }

        return $str_part_one . $str_part_two;
    }

    /**
     * Lowercase for all words in the string.
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string[]    $exceptions                    [optional] <p>Exclusion for some words.</p>
     * @param string      $char_list                     [optional] <p>Additional chars that contains to words and do
     *                                                   not start a new word.</p>
     * @param string      $encoding                      [optional] <p>Set the charset.</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function lcwords(
        string $str,
        array $exceptions = [],
        string $char_list = '',
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if (!$str) {
            return '';
        }

        $words = self::str_to_words($str, $char_list);
        $use_exceptions = $exceptions !== [];

        $words_str = '';
        foreach ($words as &$word) {
            if (!$word) {
                continue;
            }

            if (
                !$use_exceptions
                ||
                !\in_array($word, $exceptions, true)
            ) {
                $words_str .= self::lcfirst($word, $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length);
            } else {
                $words_str .= $word;
            }
        }

        return $words_str;
    }

    /**
     * Calculate Levenshtein distance between two strings.
     *
     * For better performance, in a real application with a single input string
     * matched against many strings from a database, you will probably want to pre-
     * encode the input only once and use \levenshtein().
     *
     * Source: https://github.com/KEINOS/mb_levenshtein
     *
     * @see https://www.php.net/manual/en/function.levenshtein
     *
     * @param string $str1            <p>One of the strings being evaluated for Levenshtein distance.</p>
     * @param string $str2            <p>One of the strings being evaluated for Levenshtein distance.</p>
     * @param int    $insertionCost   [optional] <p>Defines the cost of insertion.</p>
     * @param int    $replacementCost [optional] <p>Defines the cost of replacement.</p>
     * @param int    $deletionCost    [optional] <p>Defines the cost of deletion.</p>
     *
     * @return int
     */
    public static function levenshtein(
        string $str1,
        string $str2,
        int $insertionCost = 1,
        int $replacementCost = 1,
        int $deletionCost = 1
    ): int {
        $result = ASCII::to_ascii_remap($str1, $str2);

        return \levenshtein($result[0], $result[1], $insertionCost, $replacementCost, $deletionCost);
    }

    /**
     * Strip whitespace or other characters from the beginning of a UTF-8 string.
     *
     * EXAMPLE: <code>UTF8::ltrim('　中文空白　 '); // '中文空白　 '</code>
     *
     * @param string      $str   <p>The string to be trimmed</p>
     * @param string|null $chars <p>Optional characters to be stripped</p>
     *
     * @psalm-pure
     *
     * @return string the string with unwanted characters stripped from the left
     */
    public static function ltrim(string $str = '', ?string $chars = null): string
    {
        if ($str === '') {
            return '';
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($chars !== null) {
                /** @noinspection PregQuoteUsageInspection */
                $chars = \preg_quote($chars);
                $pattern = "^[{$chars}]+";
            } else {
                $pattern = '^[\\s]+';
            }

            return (string) \mb_ereg_replace($pattern, '', $str);
        }

        if ($chars !== null) {
            $chars = \preg_quote($chars, '/');
            $pattern = "^[{$chars}]+";
        } else {
            $pattern = '^[\\s]+';
        }

        return self::regexReplace($str, $pattern, '');
    }

    /**
     * Returns the UTF-8 character with the maximum code point in the given data.
     *
     * EXAMPLE: <code>UTF8::max('abc-äöü-中文空白'); // 'ø'</code>
     *
     * @param string|string[] $arg <p>A UTF-8 encoded string or an array of such strings.</p>
     *
     * @psalm-pure
     *
     * @return string|null the character with the highest code point than others, returns null on failure or empty input
     */
    public static function max($arg)
    {
        if (\is_array($arg)) {
            $arg = \implode('', $arg);
        }

        $codepoints = self::codepoints($arg);
        if ($codepoints === []) {
            return null;
        }

        $codepoint_max = \max($codepoints);

        return self::chr((int) $codepoint_max);
    }

    /**
     * Calculates and returns the maximum number of bytes taken by any
     * UTF-8 encoded character in the given string.
     *
     * EXAMPLE: <code>UTF8::max_chr_width('Intërnâtiônàlizætiøn'); // 2</code>
     *
     * @param string $str <p>The original Unicode string.</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <p>Max byte lengths of the given chars.</p>
     *
     * @phpstan-return 0|1|2|3|4
     */
    public static function max_chr_width(string $str): int
    {
        $bytes = self::chrSizeList($str);
        if ($bytes !== []) {
            return (int) \max($bytes);
        }

        return 0;
    }

    /**
     * Checks whether mbstring is available on the server.
     *
     * @psalm-pure
     *
     * @return bool
     *              <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>
     *
     * @internal <p>Please do not use it anymore, we will make is private in next major version.</p>
     */
    public static function mbstring_loaded(): bool
    {
        return \extension_loaded('mbstring');
    }

    /**
     * Returns the UTF-8 character with the minimum code point in the given data.
     *
     * EXAMPLE: <code>UTF8::min('abc-äöü-中文空白'); // '-'</code>
     *
     * @param string|string[] $arg <strong>A UTF-8 encoded string or an array of such strings.</strong>
     *
     * @psalm-pure
     *
     * @return string|null
     *                     <p>The character with the lowest code point than others, returns null on failure or empty input.</p>
     */
    public static function min($arg)
    {
        if (\is_array($arg)) {
            $arg = \implode('', $arg);
        }

        $codepoints = self::codepoints($arg);
        if ($codepoints === []) {
            return null;
        }

        $codepoint_min = \min($codepoints);

        return self::chr((int) $codepoint_min);
    }

    /**
     * Normalize the encoding-"name" input.
     *
     * EXAMPLE: <code>UTF8::normalize_encoding('UTF8'); // 'UTF-8'</code>
     *
     * @param mixed $encoding <p>e.g.: ISO, UTF8, WINDOWS-1251 etc.</p>
     * @param mixed $fallback <p>e.g.: UTF-8</p>
     *
     * @psalm-pure
     *
     * @return mixed|string
     *                      <p>e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.<br>Will return a empty string as fallback (by default)</p>
     *
     * @template TNormalizeEncodingFallback
     * @phpstan-param string|TNormalizeEncodingFallback $fallback
     * @phpstan-return string|TNormalizeEncodingFallback
     */
    public static function normalize_encoding($encoding, $fallback = '')
    {
        /**
         * @psalm-suppress ImpureStaticVariable
         *
         * @var array<string,string>
         */
        static $STATIC_NORMALIZE_ENCODING_CACHE = [];

        // init
        $encoding = (string) $encoding;

        if (!$encoding) {
            return $fallback;
        }

        if (
            $encoding === self::UTF8
            ||
            $encoding === self::UTF8_ALT
        ) {
            return self::UTF8;
        }

        if (
            $encoding === '8BIT'
            ||
            $encoding === 'BINARY'
        ) {
            return self::CP850;
        }

        if (
            $encoding === 'HTML'
            ||
            $encoding === self::HTML_ENTITIES
        ) {
            return self::HTML_ENTITIES;
        }

        if (
            $encoding === 'ISO'
            ||
            $encoding === self::ISO88591
        ) {
            return self::ISO88591;
        }

        // only a fallback, for non "strict_types" usage ...
        if ($encoding === '1') {
            return $fallback;
        }

        if (isset($STATIC_NORMALIZE_ENCODING_CACHE[$encoding])) {
            return $STATIC_NORMALIZE_ENCODING_CACHE[$encoding];
        }

        if (self::$ENCODINGS === null) {
            self::$ENCODINGS = self::getData('encodings');
        }

        if (\in_array($encoding, self::$ENCODINGS, true)) {
            $STATIC_NORMALIZE_ENCODING_CACHE[$encoding] = $encoding;

            return $encoding;
        }

        $encoding_original = $encoding;
        $encoding = \strtoupper($encoding);
        $encoding_upper_helper = (string) \preg_replace('/[^a-zA-Z0-9]/u', '', $encoding);

        $equivalences = [
            'ISO8859'     => 'ISO-8859-1',
            'ISO88591'    => 'ISO-8859-1',
            'ISO'         => 'ISO-8859-1',
            'LATIN'       => 'ISO-8859-1',
            'LATIN1'      => 'ISO-8859-1', // Western European
            'ISO88592'    => 'ISO-8859-2',
            'LATIN2'      => 'ISO-8859-2', // Central European
            'ISO88593'    => 'ISO-8859-3',
            'LATIN3'      => 'ISO-8859-3', // Southern European
            'ISO88594'    => 'ISO-8859-4',
            'LATIN4'      => 'ISO-8859-4', // Northern European
            'ISO88595'    => 'ISO-8859-5',
            'ISO88596'    => 'ISO-8859-6', // Greek
            'ISO88597'    => 'ISO-8859-7',
            'ISO88598'    => 'ISO-8859-8', // Hebrew
            'ISO88599'    => 'ISO-8859-9',
            'LATIN5'      => 'ISO-8859-9', // Turkish
            'ISO885911'   => 'ISO-8859-11',
            'TIS620'      => 'ISO-8859-11', // Thai
            'ISO885910'   => 'ISO-8859-10',
            'LATIN6'      => 'ISO-8859-10', // Nordic
            'ISO885913'   => 'ISO-8859-13',
            'LATIN7'      => 'ISO-8859-13', // Baltic
            'ISO885914'   => 'ISO-8859-14',
            'LATIN8'      => 'ISO-8859-14', // Celtic
            'ISO885915'   => 'ISO-8859-15',
            'LATIN9'      => 'ISO-8859-15', // Western European (with some extra chars e.g. €)
            'ISO885916'   => 'ISO-8859-16',
            'LATIN10'     => 'ISO-8859-16', // Southeast European
            'CP1250'      => 'WINDOWS-1250',
            'WIN1250'     => 'WINDOWS-1250',
            'WINDOWS1250' => 'WINDOWS-1250',
            'CP1251'      => 'WINDOWS-1251',
            'WIN1251'     => 'WINDOWS-1251',
            'WINDOWS1251' => 'WINDOWS-1251',
            'CP1252'      => 'WINDOWS-1252',
            'WIN1252'     => 'WINDOWS-1252',
            'WINDOWS1252' => 'WINDOWS-1252',
            'CP1253'      => 'WINDOWS-1253',
            'WIN1253'     => 'WINDOWS-1253',
            'WINDOWS1253' => 'WINDOWS-1253',
            'CP1254'      => 'WINDOWS-1254',
            'WIN1254'     => 'WINDOWS-1254',
            'WINDOWS1254' => 'WINDOWS-1254',
            'CP1255'      => 'WINDOWS-1255',
            'WIN1255'     => 'WINDOWS-1255',
            'WINDOWS1255' => 'WINDOWS-1255',
            'CP1256'      => 'WINDOWS-1256',
            'WIN1256'     => 'WINDOWS-1256',
            'WINDOWS1256' => 'WINDOWS-1256',
            'CP1257'      => 'WINDOWS-1257',
            'WIN1257'     => 'WINDOWS-1257',
            'WINDOWS1257' => 'WINDOWS-1257',
            'CP1258'      => 'WINDOWS-1258',
            'WIN1258'     => 'WINDOWS-1258',
            'WINDOWS1258' => 'WINDOWS-1258',
            'UTF16'       => 'UTF-16',
            'UTF32'       => 'UTF-32',
            'UTF8'        => 'UTF-8',
            'UTF'         => 'UTF-8',
            'UTF7'        => 'UTF-7',
            '8BIT'        => 'CP850',
            'BINARY'      => 'CP850',
        ];

        if (!empty($equivalences[$encoding_upper_helper])) {
            $encoding = $equivalences[$encoding_upper_helper];
        }

        $STATIC_NORMALIZE_ENCODING_CACHE[$encoding_original] = $encoding;

        return $encoding;
    }

    /**
     * Standardizes line endings to a specified format.
     *
     * Converts all variations of line endings (`\r\n`, `\r`, `\n`) to a uniform format.
     *
     * @param string          $str      The input string.
     * @param string|string[] $replacer The replacement character(s) for line endings. Defaults to "\n" (Unix-style).
     *
     * @return string The string with normalized line endings.
     */
    public static function normalizeLineEnding(string $str, string|array $replacer = "\n"): string
    {
        return str_replace(["\r\n", "\r", "\n"], $replacer, $str);
    }

    /**
     * Normalize some MS Word special characters.
     *
     * EXAMPLE: <code>UTF8::normalizeMsWord('„Abcdef…”'); // '"Abcdef..."'</code>
     *
     * @param string $str <p>The string to be normalized.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with normalized characters for commonly used chars in Word documents.</p>
     */
    public static function normalizeMsWord(string $str): string
    {
        return ASCII::normalizeMsWord($str);
    }

    /**
     * Normalize the whitespace in a string.
     *
     * EXAMPLE: <code>UTF8::normalizeWhitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true); // "abc-\xc2\xa0-öäü- -"</code>
     *
     * @param string $str The string to be normalized.
     * @param bool $keepNonBreakingSpace [optional] Set to true to keep non-breaking spaces.
     * @param bool $keepBidiUnicodeControls [optional] Set to true to keep bidirectional Unicode controls.
     * @param bool $normalizeControlCharacters [optional] Set to true to normalize control characters.
     *
     * @psalm-pure
     *
     * @return string A string with normalized whitespace.
     */
    public static function normalizeWhitespace(
        string $str,
        bool $keepNonBreakingSpace = false,
        bool $keepBidiUnicodeControls = false,
        bool $normalizeControlCharacters = false
    ): string {
        // Direct call to the ASCII class's normalizeWhitespace method
        return ASCII::normalizeWhitespace(
            $str,
            $keepNonBreakingSpace,
            $keepBidiUnicodeControls,
            $normalizeControlCharacters
        );
    }

    /**
     * Calculates Unicode code point of the given UTF-8 encoded character.
     *
     * INFO: opposite to UTF8::chr()
     *
     * EXAMPLE: <code>UTF8::ord('☃'); // 0x2603</code>
     *
     * @param string $chr      <p>The character of which to calculate code point.<p/>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <p>Unicode code point of the given character,<br>
     *             0 on invalid UTF-8 byte sequence</p>
     */
    public static function ord($chr, string $encoding = self::UTF8): int
    {
        /**
         * @psalm-suppress ImpureStaticVariable
         *
         * @var array<string,int>
         */
        static $CHAR_CACHE = [];

        // init
        $chr = (string) $chr;

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        $cache_key = $chr . '_' . $encoding;
        if (isset($CHAR_CACHE[$cache_key])) {
            return $CHAR_CACHE[$cache_key];
        }

        // check again, if it's still not UTF-8
        if ($encoding !== self::UTF8) {
            $chr = self::encode($encoding, $chr);
        }

        if (self::$ORD === null) {
            self::$ORD = self::getData('ord');
        }

        if (isset(self::$ORD[$chr])) {
            return $CHAR_CACHE[$cache_key] = self::$ORD[$chr];
        }

        //
        // fallback via "IntlChar"
        //

        if (self::$SUPPORT[self::FEATURE_INTLCHAR] === true) {
            $code = \IntlChar::ord($chr);
            if ($code) {
                return $CHAR_CACHE[$cache_key] = $code;
            }
        }

        //
        // fallback via vanilla php
        //

        $chr = \unpack('C*', (string) \substr($chr, 0, 4));
        /** @noinspection PhpSillyAssignmentInspection - hack for phpstan */
        /** @var int[] $chr - "unpack": only false if the format string contains errors */
        $chr = $chr;
        $code = $chr ? $chr[1] : 0;

        if ($code >= 0xF0 && isset($chr[4])) {
            return $CHAR_CACHE[$cache_key] = ((($code - 0xF0) << 18) + (($chr[2] - 0x80) << 12) + (($chr[3] - 0x80) << 6) + $chr[4] - 0x80);
        }

        if ($code >= 0xE0 && isset($chr[3])) {
            return $CHAR_CACHE[$cache_key] = ((($code - 0xE0) << 12) + (($chr[2] - 0x80) << 6) + $chr[3] - 0x80);
        }

        if ($code >= 0xC0 && isset($chr[2])) {
            return $CHAR_CACHE[$cache_key] = ((($code - 0xC0) << 6) + $chr[2] - 0x80);
        }

        return $CHAR_CACHE[$cache_key] = $code;
    }

    /**
     * Parses the string into an array (into the the second parameter).
     *
     * WARNING: Unlike "parse_str()", this method does not (re-)place variables in the current scope,
     *          if the second parameter is not set!
     *
     * EXAMPLE: <code>
     * UTF8::parse_str('Iñtërnâtiônéàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ', $array);
     * echo $array['Iñtërnâtiônéàlizætiøn']; // '測試'
     * </code>
     *
     * @see http://php.net/manual/en/function.parse-str.php
     *
     * @param string               $str        <p>The input string.</p>
     * @param array<string, mixed> $result     <p>The result will be returned into this reference parameter.</p>
     * @param bool                 $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Will return <strong>false</strong> if php can't parse the string and we haven't any $result.</p>
     */
    public static function parse_str(string $str, &$result, bool $clean_utf8 = false): bool
    {
        if ($clean_utf8) {
            $str = self::clean($str);
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            $return = \mb_parse_str($str, $result);

            return $return !== false && $result !== [];
        }

        /**
         * @psalm-suppress ImpureFunctionCall - we use the second parameter, so we don't change variables by magic
         */
        \parse_str($str, $result);

        return $result !== [];
    }

    /**
     * Checks if \u modifier is available that enables Unicode support in PCRE.
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>
     *              <strong>true</strong> if support is available,<br>
     *              <strong>false</strong> otherwise
     *              </p>
     */
    public static function pcre_utf8_support(): bool
    {
        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        return (bool) @\preg_match('//u', '');
    }

    /**
     * Create an array containing a range of UTF-8 characters.
     *
     * EXAMPLE: <code>UTF8::range('κ', 'ζ'); // array('κ', 'ι', 'θ', 'η', 'ζ',)</code>
     *
     * @param int|string $var1      <p>Numeric or hexadecimal code points, or a UTF-8 character to start from.</p>
     * @param int|string $var2      <p>Numeric or hexadecimal code points, or a UTF-8 character to end at.</p>
     * @param bool       $use_ctype <p>use ctype to detect numeric and hexadecimal, otherwise we will use a simple
     *                              "is_numeric"</p>
     * @param string     $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param float|int  $step      [optional] <p>
     *                              If a step value is given, it will be used as the
     *                              increment between elements in the sequence. step
     *                              should be given as a positive number. If not specified,
     *                              step will default to 1.
     *                              </p>
     *
     * @psalm-pure
     *
     * @return list<string>
     */
    public static function range(
        $var1,
        $var2,
        bool $use_ctype = true,
        string $encoding = self::UTF8,
        $step = 1
    ): array {
        if (!$var1 || !$var2) {
            return [];
        }

        if ($step !== 1) {
            /**
             * @psalm-suppress RedundantConditionGivenDocblockType
             * @psalm-suppress DocblockTypeContradiction
             * @phpstan-ignore-next-line | ignore wrong inputs
             */
            if (!\is_numeric($step)) {
                throw new \InvalidArgumentException('$step need to be a number, type given: ' . \gettype($step));
            }

            /**
             * @psalm-suppress RedundantConditionGivenDocblockType - false-positive from psalm?
             */
            if ($step <= 0) {
                throw new \InvalidArgumentException('$step need to be a positive number, given: ' . $step);
            }
        }

        if ($use_ctype && self::$SUPPORT[self::FEATURE_CTYPE] === false) {
            throw new \RuntimeException('ext-ctype: is not installed');
        }

        $is_digit = false;
        $is_xdigit = false;

        if ($use_ctype && \ctype_digit((string) $var1) && \ctype_digit((string) $var2)) {
            $is_digit = true;
            $start = (int) $var1;
        } elseif ($use_ctype && \ctype_xdigit($var1) && \ctype_xdigit($var2)) {
            $is_xdigit = true;
            $start = (int) self::hex_to_int((string) $var1);
        } elseif (!$use_ctype && \is_numeric($var1)) {
            $start = (int) $var1;
        } else {
            $start = self::ord((string) $var1);
        }

        if (!$start) {
            return [];
        }

        if ($is_digit) {
            $end = (int) $var2;
        } elseif ($is_xdigit) {
            $end = (int) self::hex_to_int((string) $var2);
        } elseif (!$use_ctype && \is_numeric($var2)) {
            $end = (int) $var2;
        } else {
            $end = self::ord((string) $var2);
        }

        if (!$end) {
            return [];
        }

        $array = [];
        foreach (\range($start, $end, $step) as $i) {
            $array[] = (string) self::chr((int) $i, $encoding);
        }

        return $array;
    }

    /**
     * Get data from an array via array like string.
     *
     * EXAMPLE: <code>$array['foo'][123] = 'lall'; UTF8::getUrlParamFromArray('foo[123]', $array); // 'lall'</code>
     *
     * @param array<array-key, mixed> $data
     *
     * @return mixed
     */
    public static function getUrlParamFromArray(string $param, array $data)
    {
        /**
         * @param array<array-key, mixed> $searchArray
         * @param array<array-key, mixed> $array
         *
         * @return mixed
         */
        $getUrlArgFromArrayHelper = static function (array $searchArray, array $array) use (&$getUrlArgFromArrayHelper) {
            foreach ($searchArray as $key => $value) {
                if (isset($array[$key])) {
                    if (\is_array($value) && \is_array($array[$key])) {
                        return $getUrlArgFromArrayHelper($value, $array[$key]);
                    }

                    return $array[$key];
                }
            }

            return null;
        };

        /**
         * @param string $string
         * @return array|null
         */
        $getUrlKeyArgsFromString = static function (string $string) {
            if (!self::str_contains($string, '?')) {
                $string = '?' . $string;
            }

            $args = parse_url($string, PHP_URL_QUERY);
            if ($args) {
                $query = [];
                parse_str($args, $query);

                return $query;
            }

            return null;
        };

        if (isset($data[$param])) {
            return $data[$param];
        }

        $paramKeys = $getUrlKeyArgsFromString($param);
        if ($paramKeys !== null) {
            return $getUrlArgFromArrayHelper($paramKeys, $data);
        }

        return null;
    }

    /**
     * Multi decode HTML entity + fix urlencoded-win1252-chars.
     *
     * EXAMPLE: <code>UTF8::rawurldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest+test'</code>
     *
     * e.g:
     * 'test+test'                     => 'test+test'
     * 'D&#252;sseldorf'               => 'Düsseldorf'
     * 'D%FCsseldorf'                  => 'Düsseldorf'
     * 'D&#xFC;sseldorf'               => 'Düsseldorf'
     * 'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
     * 'DÃ¼sseldorf'                   => 'Düsseldorf'
     * 'D%C3%BCsseldorf'               => 'Düsseldorf'
     * 'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
     * 'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'
     *
     * @param string $str          <p>The input string.</p>
     * @param bool   $multi_decode <p>Decode as often as possible.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The decoded URL, as a string.</p>
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function rawurldecode(string $str, bool $multi_decode = true): string
    {
        if ($str === '') {
            return '';
        }

        $str = self::urlDecodeUnicodeHelper($str);

        if ($multi_decode) {
            do {
                $str_compare = $str;

                /**
                 * @psalm-suppress PossiblyInvalidArgument
                 */
                $str = \rawurldecode(
                    self::htmlEntityDecode(
                        self::toUtf8($str),
                        \ENT_QUOTES | \ENT_HTML5
                    )
                );
            } while ($str_compare !== $str);
        } else {
            /**
             * @psalm-suppress PossiblyInvalidArgument
             */
            $str = \rawurldecode(
                self::htmlEntityDecode(
                    self::toUtf8($str),
                    \ENT_QUOTES | \ENT_HTML5
                )
            );
        }

        return self::fixSimpleUtf8($str);
    }

    /**
     * Replaces all occurrences of $pattern in $str by $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $pattern     <p>The regular expression pattern.</p>
     * @param string $replacement <p>The string to replace with.</p>
     * @param string $options     [optional] <p>Matching conditions to be used.</p>
     * @param string $delimiter   [optional] <p>Delimiter for the regex. Default: '/'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function regexReplace(
        string $str,
        string $pattern,
        string $replacement,
        string $options = '',
        string $delimiter = '/'
    ): string {
        // If the options are set to 'msr', change it to 'ms'
        if ($options === 'msr') {
            $options = 'ms';
        }

        // Default delimiter fallback
        $delimiter = $delimiter ?: '/';

        return (string) preg_replace(
            "{$delimiter}{$pattern}{$delimiter}u{$options}",
            $replacement,
            $str
        );
    }

    /**
     * Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.
     *
     * EXAMPLE: <code>UTF8::removeBom("\xEF\xBB\xBFΜπορώ να"); // 'Μπορώ να'</code>
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return string A string without UTF-BOM.
     */
    public static function removeBom(string $str): string
    {
        if ($str === '') {
            return '';
        }

        $strLength = strlen($str);

        foreach (self::$BOM as $bomString => $bomByteLength) {
            if (strncmp($str, $bomString, $bomByteLength) === 0) {
                $strTmp = substr($str, $bomByteLength, $strLength - $bomByteLength);

                if ($strTmp === false) {
                    return '';
                }

                $str = (string) $strTmp;
            }
        }

        return $str;
    }

    /**
     * Removes duplicate occurrences of a string in another string.
     *
     * EXAMPLE: <code>UTF8::remove_duplicates('öäü-κόσμεκόσμε-äöü', 'κόσμε'); // 'öäü-κόσμε-äöü'</code>
     *
     * @param string          $str  <p>The base string.</p>
     * @param string|string[] $what <p>String to search for in the base string.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with removed duplicates.</p>
     */
    public static function remove_duplicates(string $str, $what = ' '): string
    {
        if (\is_string($what)) {
            $what = [$what];
        }

        /**
         * @psalm-suppress RedundantConditionGivenDocblockType
         * @phpstan-ignore-next-line | ignore wrong inputs
         */
        if (\is_array($what)) {
            foreach ($what as $item) {
                $str = (string) \preg_replace('/(' . \preg_quote($item, '/') . ')+/u', $item, $str);
            }
        }

        return $str;
    }

    /**
     * Remove html via "strip_tags()" from the string.
     *
     * @param string $str            <p>The input string.</p>
     * @param string $allowable_tags [optional] <p>You can use the optional second parameter to specify tags which
     *                               should not be stripped. Default: null
     *                               </p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with without html tags.</p>
     */
    public static function remove_html(string $str, string $allowable_tags = ''): string
    {
        return \strip_tags($str, $allowable_tags);
    }

    /**
     * Remove all breaks [<br> | \r\n | \r | \n | ...] from the string.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $replacement [optional] <p>Default is a empty string.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string without breaks.</p>
     */
    public static function remove_html_breaks(string $str, string $replacement = ''): string
    {
        return (string) \preg_replace("#/\r\n|\r|\n|<br.*/?>#isU", $replacement, $str);
    }

    /**
     * Removes invisible characters from a string.
     *
     * Prevents null character sandwiching (e.g., "Java\0script").
     *
     * Example: UTF8::removeInvisibleCharacters("κόσ\0με"); // 'κόσμε'
     *
     * @param string $str The input string.
     * @param bool $urlEncoded Whether to remove URL-encoded control characters (default: false).
     *                         WARNING: May cause false positives (e.g., 'aa%0Baa' → 'aaaa').
     * @param string $replacement The character used for replacement (default: '').
     * @param bool $keepBasicControlCharacters Whether to keep basic control characters like [LRM] or [LSEP] (default: true).
     *
     * @psalm-pure
     *
     * @return string A string without invisible characters.
     */
    public static function removeInvisibleCharacters(
        string $str,
        bool $urlEncoded = false,
        string $replacement = '',
        bool $keepBasicControlCharacters = true
    ): string {
        return ASCII::removeInvisibleCharacters($str, $urlEncoded, $replacement, $keepBasicControlCharacters);
    }

    /**
     * Returns a new string with the prefix $substring removed, if present.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $substring <p>The prefix to remove.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string without the prefix $substring.</p>
     */
    public static function remove_left(
        string $str,
        string $substring,
        string $encoding = self::UTF8
    ): string {
        if (
            $substring
            &&
            \strpos($str, $substring) === 0
        ) {
            if ($encoding === self::UTF8) {
                return (string) \mb_substr(
                    $str,
                    (int) \mb_strlen($substring)
                );
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return (string) self::substr(
                $str,
                (int) self::strlen($substring, $encoding),
                null,
                $encoding
            );
        }

        return $str;
    }

    /**
     * Returns a new string with the suffix $substring removed, if present.
     *
     * @param string $str
     * @param string $substring <p>The suffix to remove.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string having a $str without the suffix $substring.</p>
     */
    public static function remove_right(
        string $str,
        string $substring,
        string $encoding = self::UTF8
    ): string {
        if ($substring && \substr($str, -\strlen($substring)) === $substring) {
            if ($encoding === self::UTF8) {
                return (string) \mb_substr(
                    $str,
                    0,
                    (int) \mb_strlen($str) - (int) \mb_strlen($substring)
                );
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return (string) self::substr(
                $str,
                0,
                (int) self::strlen($str, $encoding) - (int) self::strlen($substring, $encoding),
                $encoding
            );
        }

        return $str;
    }

    /**
     * Returns a new string with the suffix $substring removed, if present and case-insensitive.
     *
     * @param string $str
     * @param string $substring <p>The suffix to remove.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string having a $str without the suffix $substring.</p>
     */
    public static function remove_iright(
        string $str,
        string $substring,
        string $encoding = self::UTF8
    ): string {
        if ($substring && self::strtoupper(\substr($str, -\strlen($substring)), $encoding) === self::strtoupper($substring, $encoding)) {
            if ($encoding === self::UTF8) {
                return (string) \mb_substr(
                    $str,
                    0,
                    (int) \mb_strlen($str) - (int) \mb_strlen($substring)
                );
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return (string) self::substr(
                $str,
                0,
                (int) self::strlen($str, $encoding) - (int) self::strlen($substring, $encoding),
                $encoding
            );
        }

        return $str;
    }

    /**
     * Returns a new string with the prefix $substring removed, if present and case-insensitive.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $substring <p>The prefix to remove.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string without the prefix $substring.</p>
     */
    public static function remove_ileft(
        string $str,
        string $substring,
        string $encoding = self::UTF8
    ): string {
        if (
            $substring
            &&
            \strpos(self::strtoupper($str, $encoding), self::strtoupper($substring, $encoding)) === 0
        ) {
            if ($encoding === self::UTF8) {
                return (string) \mb_substr(
                    $str,
                    (int) \mb_strlen($substring)
                );
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return (string) self::substr(
                $str,
                (int) self::strlen($substring, $encoding),
                null,
                $encoding
            );
        }

        return $str;
    }

    /**
     * Replaces all occurrences of $search in $str by $replacement.
     *
     * @param string $str            <p>The input string.</p>
     * @param string $search         <p>The needle to search for.</p>
     * @param string $replacement    <p>The string to replace with.</p>
     * @param bool   $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with replaced parts.</p>
     */
    public static function replace(
        string $str,
        string $search,
        string $replacement,
        bool $case_sensitive = true
    ): string {
        if ($case_sensitive) {
            return \str_replace($search, $replacement, $str);
        }

        return self::str_ireplace($search, $replacement, $str);
    }

    /**
     * Replaces all occurrences of $search in $str by $replacement.
     *
     * @param string          $str            <p>The input string.</p>
     * @param string[]        $search         <p>The elements to search for.</p>
     * @param string|string[] $replacement    <p>The string to replace with.</p>
     * @param bool            $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with replaced parts.</p>
     */
    public static function replace_all(
        string $str,
        array $search,
        $replacement,
        bool $case_sensitive = true
    ): string {
        if ($case_sensitive) {
            return \str_replace($search, $replacement, $str);
        }

        return self::str_ireplace($search, $replacement, $str);
    }

    /**
     * Replaces the diamond question mark (�) and invalid UTF-8 characters with a given replacement.
     *
     * Example: UTF8::replaceDiamondQuestionMark('中文空白�', ''); // '中文空白'
     *
     * @param string $str The input string.
     * @param string $replacementChar The character used for replacement. Default: '' (empty string).
     * @param bool $processInvalidUtf8Chars Whether to convert invalid UTF-8 characters. Default: true.
     *
     * @psalm-pure
     *
     * @return string A string without diamond question marks (�) or invalid UTF-8 characters.
     */
    public static function replaceDiamondQuestionMark(
        string $str,
        string $replacementChar = '',
        bool $processInvalidUtf8Chars = true
    ): string {
        if ($str === '') {
            return '';
        }

        if ($processInvalidUtf8Chars) {
            $replacementHelper = $replacementChar === '' ? 'none' : ord($replacementChar);

            // Clean the string if mbstring support is unavailable
            if (!self::$SUPPORT[self::FEATURE_MBSTRING]) {
                $str = self::clean($str);
            }

            // Handle invalid UTF-8 character replacement
            $save = mb_substitute_character();
            @mb_substitute_character($replacementHelper);
            $str = (string) mb_convert_encoding($str, self::UTF8, self::UTF8);
            mb_substitute_character($save);
        }

        return str_replace(["\xEF\xBF\xBD", '�'], $replacementChar, $str);
    }

    /**
     * Strip whitespace or other characters from the end of a UTF-8 string.
     *
     * EXAMPLE: <code>UTF8::rtrim('-ABC-中文空白-  '); // '-ABC-中文空白-'</code>
     *
     * @param string      $str   <p>The string to be trimmed.</p>
     * @param string|null $chars <p>Optional characters to be stripped.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with unwanted characters stripped from the right.</p>
     */
    public static function rtrim(string $str = '', ?string $chars = null): string
    {
        if ($str === '') {
            return '';
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($chars !== null) {
                /** @noinspection PregQuoteUsageInspection */
                $chars = \preg_quote($chars);
                $pattern = "[{$chars}]+$";
            } else {
                $pattern = '[\\s]+$';
            }

            return (string) \mb_ereg_replace($pattern, '', $str);
        }

        if ($chars !== null) {
            $chars = \preg_quote($chars, '/');
            $pattern = "[{$chars}]+$";
        } else {
            $pattern = '[\\s]+$';
        }

        return self::regexReplace($str, $pattern, '');
    }

    /**
     * WARNING: Print native UTF-8 support (libs) by default, e.g. for debugging.
     *
     * @param bool $useEcho
     *
     * @psalm-pure
     *
     * @return string|void
     *
     * @phpstan-return ($useEcho is true ? void : string)
     */
    public static function showSupport(bool $useEcho = true)
    {
        // init
        $html = '';

        $html .= '<pre>';
        foreach (self::$SUPPORT as $key => &$value) {
            $html .= $key . ' - ' . \print_r($value, true) . "\n<br>";
        }
        $html .= '</pre>';

        if ($useEcho) {
            echo $html;
        }

        return $html;
    }

    /**
     * Converts a UTF-8 character to an HTML Numbered Entity like "&#123;".
     *
     * EXAMPLE: <code>UTF8::singleChrHtmlEncode('κ'); // '&#954;'</code>
     *
     * @param string $char            The Unicode character to be encoded as a numbered entity.
     * @param bool   $keepAsciiChars  Whether to keep ASCII characters unchanged.
     * @param string $encoding        [optional] Character set for encoding functions.
     *
     * @psalm-pure
     *
     * @return string The HTML numbered entity for the given character.
     *
     * @template T as string
     * @phpstan-param T $char
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function singleChrHtmlEncode(
        string $char,
        bool $keepAsciiChars = false,
        string $encoding = self::UTF8
    ): string {
        if ($char === '') {
            return '';
        }

        if ($keepAsciiChars && ASCII::isAscii($char)) {
            return $char;
        }

        return '&#' . self::ord($char, $encoding) . ';';
    }


    /**
     * @param string      $str
     * @param int<1, max> $tab_length
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function spaces_to_tabs(string $str, int $tab_length = 4): string
    {
        if ($tab_length === 4) {
            $tab = '    ';
        } elseif ($tab_length === 2) {
            $tab = '  ';
        } else {
            $tab = \str_repeat(' ', $tab_length);
        }

        return \str_replace($tab, "\t", $str);
    }

    /**
     * Returns a camelCase version of the string. Trims surrounding spaces,
     * capitalizes letters following digits, spaces, dashes and underscores,
     * and removes spaces, dashes, as well as underscores.
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string      $encoding                      [optional] <p>Default: 'UTF-8'</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_camelize(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if ($clean_utf8) {
            $str = self::clean($str);
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        $str = self::lcfirst(
            \trim($str),
            $encoding,
            false,
            $lang,
            $try_to_keep_the_string_length
        );
        $str = (string) \preg_replace('/^[-_]+/', '', $str);

        $use_mb_functions = $lang === null && !$try_to_keep_the_string_length;

        $str = (string) \preg_replace_callback(
            '/[-_\\s]+(.)?/u',
            /**
             * @param array $match
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $match) use ($use_mb_functions, $encoding, $lang, $try_to_keep_the_string_length): string {
                if (isset($match[1])) {
                    if ($use_mb_functions) {
                        if ($encoding === self::UTF8) {
                            return \mb_strtoupper($match[1]);
                        }

                        return \mb_strtoupper($match[1], $encoding);
                    }

                    return self::strtoupper($match[1], $encoding, false, $lang, $try_to_keep_the_string_length);
                }

                return '';
            },
            $str
        );

        return (string) \preg_replace_callback(
            '/[\\p{N}]+(.)?/u',
            /**
             * @param array $match
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $match) use ($use_mb_functions, $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length): string {
                if ($use_mb_functions) {
                    if ($encoding === self::UTF8) {
                        return \mb_strtoupper($match[0]);
                    }

                    return \mb_strtoupper($match[0], $encoding);
                }

                return self::strtoupper($match[0], $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length);
            },
            $str
        );
    }

    /**
     * Returns the string with the first letter of each word capitalized,
     * except for when the word is a name which shouldn't be capitalized.
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with $str capitalized.</p>
     */
    public static function str_capitalize_name(string $str): string
    {
        return self::str_capitalize_name_helper(
            self::str_capitalize_name_helper(
                self::collapseWhitespace($str),
                ' '
            ),
            '-'
        );
    }

    /**
     * Returns true if the string contains $needle, false otherwise. By default
     * the comparison is case-sensitive, but can be made insensitive by setting
     * $case_sensitive to false.
     *
     * @param string $haystack       <p>The input string.</p>
     * @param string $needle         <p>Substring to look for.</p>
     * @param bool   $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $haystack contains $needle.</p>
     */
    public static function str_contains(
        string $haystack,
        string $needle,
        bool $case_sensitive = true
    ): bool {
        if ($case_sensitive) {
            if (\PHP_VERSION_ID >= 80000) {
                /** @phpstan-ignore-next-line - only for PHP8 */
                return \str_contains($haystack, $needle);
            }

            return \strpos($haystack, $needle) !== false;
        }

        return \mb_stripos($haystack, $needle) !== false;
    }

    /**
     * Returns true if the string contains all $needles, false otherwise. By
     * default, the comparison is case-sensitive, but can be made insensitive by
     * setting $case_sensitive to false.
     *
     * @param string   $haystack       <p>The input string.</p>
     * @param scalar[] $needles        <p>SubStrings to look for.</p>
     * @param bool     $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $haystack contains $needle.</p>
     */
    public static function str_contains_all(
        string $haystack,
        array $needles,
        bool $case_sensitive = true
    ): bool {
        if ($haystack === '' || $needles === []) {
            return false;
        }

        foreach ($needles as &$needle) {
            if (
                $case_sensitive
                &&
                (!$needle || \strpos($haystack, (string)$needle) === false)
            ) {
                return false;
            }

            if (!$needle || \mb_stripos($haystack, (string) $needle) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns true if the string contains any $needles, false otherwise. By
     * default the comparison is case-sensitive, but can be made insensitive by
     * setting $case_sensitive to false.
     *
     * @param string   $haystack       <p>The input string.</p>
     * @param scalar[] $needles        <p>SubStrings to look for.</p>
     * @param bool     $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str contains $needle.</p>
     */
    public static function str_contains_any(
        string $haystack,
        array $needles,
        bool $case_sensitive = true
    ): bool {
        if ($haystack === '' || $needles === []) {
            return false;
        }

        foreach ($needles as &$needle) {
            if (!$needle) {
                continue;
            }

            if ($case_sensitive) {
                if (\strpos($haystack, (string) $needle) !== false) {
                    return true;
                }

                continue;
            }

            if (\mb_stripos($haystack, (string) $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a lowercase and trimmed string separated by dashes. Dashes are
     * inserted before uppercase characters (with the exception of the first
     * character of the string), and in place of spaces as well as underscores.
     *
     * @param string $str      <p>The input string.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_dasherize(string $str, string $encoding = self::UTF8): string
    {
        return self::str_delimit($str, '-', $encoding);
    }

    /**
     * Returns a lowercase and trimmed string separated by the given delimiter.
     *
     * Delimiters are inserted before uppercase characters (with the exception
     * of the first character of the string), and in place of spaces, dashes,
     * and underscores. Alpha delimiters are not converted to lowercase.
     *
     * EXAMPLE: <code>
     * UTF8::str_delimit('test case, '#'); // 'test#case'
     * UTF8::str_delimit('test -case', '**'); // 'test**case'
     * </code>
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string      $delimiter                     <p>Sequence used to separate parts of the string.</p>
     * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ ->
     *                                                   ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_delimit(
        string $str,
        string $delimiter,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            $str = (string) \mb_ereg_replace('\\B(\\p{Lu})', '-\1', \trim($str));

            $use_mb_functions = $lang === null && !$try_to_keep_the_string_length;
            if ($use_mb_functions && $encoding === self::UTF8) {
                $str = \mb_strtolower($str);
            } else {
                $str = self::strtolower($str, $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length);
            }

            return (string) \mb_ereg_replace('[\\-_\\s]+', $delimiter, $str);
        }

        $str = (string) \preg_replace('/\\B(\\p{Lu})/u', '-\1', \trim($str));

        $use_mb_functions = $lang === null && !$try_to_keep_the_string_length;
        if ($use_mb_functions && $encoding === self::UTF8) {
            $str = \mb_strtolower($str);
        } else {
            $str = self::strtolower($str, $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length);
        }

        return (string) \preg_replace('/[\\-_\\s]+/u', $delimiter, $str);
    }

    /**
     * Optimized encoding detection function with support for UTF-16 and UTF-32.
     *
     * EXAMPLE: <code>
     * UTF8::detectStringEncoding('中文空白'); // 'UTF-8'
     * UTF8::detectStringEncoding('Abc'); // 'ASCII'
     * </code>
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return false|string
     *         The detected string encoding (e.g., UTF-8, UTF-16BE),
     *         or false if encoding is not detected (e.g., binary data).
     */
    public static function detectStringEncoding(string $str) {
        // 1. Check if the string is binary (e.g., UTF-16, UTF-32, PDF, Images, etc.)
        if (self::isBinary($str, !self::hasBom($str))) {
            return match (true) {
                self::isUtf32($str, false) === 1 => self::UTF32LE,
                self::isUtf32($str, false) === 2 => self::UTF32BE,
                self::isUtf16($str, false) === 1 => self::UTF16LE,
                self::isUtf16($str, false) === 2 => self::UTF16BE,
                default => false
            };
        }

        // 2. Check if the string is ASCII
        if (ASCII::isAscii($str)) {
            return self::ASCII;
        }

        // 3. Check if the string is valid UTF-8
        if (self::isUtf8String($str)) {
            return self::UTF8;
        }

        // 4. Use "mb_detect_encoding()" for additional encoding detection
        if (self::$SUPPORT[self::FEATURE_MBSTRING]) {
            $encoding = mb_detect_encoding($str, self::ENCODING_ORDER, true);
            if ($encoding !== false) {
                return $encoding;
            }
        }

        // 5. Use "iconv()" as a last resort
        self::$ENCODINGS ??= self::getData('encodings');

        foreach (self::$ENCODINGS as $encoding) {
            // Suppress errors during conversion attempt
            if (@iconv($encoding, $encoding . '//IGNORE', $str) === $str) {
                return $encoding;
            }
        }

        return false;
    }

    /**
     * Check if the string ends with the given substring.
     *
     * EXAMPLE: <code>
     * UTF8::str_ends_with('BeginMiddleΚόσμε', 'Κόσμε'); // true
     * UTF8::str_ends_with('BeginMiddleΚόσμε', 'κόσμε'); // false
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function str_ends_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($haystack === '') {
            return false;
        }

        if (\PHP_VERSION_ID >= 80000) {
            /** @phpstan-ignore-next-line - only for PHP8 */
            return \str_ends_with($haystack, $needle);
        }

        return \substr($haystack, -\strlen($needle)) === $needle;
    }

    /**
     * Returns true if the string ends with any of $substrings, false otherwise.
     *
     * - case-sensitive
     *
     * @param string   $str        <p>The input string.</p>
     * @param string[] $substrings <p>Substrings to look for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str ends with $substring.</p>
     */
    public static function str_ends_with_any(string $str, array $substrings): bool
    {
        if ($substrings === []) {
            return false;
        }

        foreach ($substrings as &$substring) {
            if (\substr($str, -\strlen($substring)) === $substring) {
                return true;
            }
        }

        return false;
    }

    /**
     * Ensures that the string begins with $substring. If it doesn't, it's
     * prepended.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $substring <p>The substring to add if not present.</p>
     *
     * @psalm-pure
     *
     * @template T as string
     * @template TSub as string
     * @phpstan-param T $str
     * @phpstan-param TSub $substring
     * @phpstan-return (TSub is non-empty-string ? non-empty-string : (T is non-empty-string ? non-empty-string : string))
     */
    public static function str_ensure_left(string $str, string $substring): string
    {
        if (
            $substring !== ''
            &&
            \strpos($str, $substring) === 0
        ) {
            return $str;
        }

        return $substring . $str;
    }

    /**
     * Ensures that the string ends with $substring. If it doesn't, it's appended.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $substring <p>The substring to add if not present.</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @template TSub as string
     * @phpstan-param T $str
     * @phpstan-param TSub $substring
     * @phpstan-return (TSub is non-empty-string ? non-empty-string : (T is non-empty-string ? non-empty-string : string))
     */
    public static function str_ensure_right(string $str, string $substring): string
    {
        if (
            $str === ''
            ||
            $substring === ''
            ||
            \substr($str, -\strlen($substring)) !== $substring
        ) {
            $str .= $substring;
        }

        return $str;
    }

    /**
     * Capitalizes the first word of the string, replaces underscores with
     * spaces, and strips '_id'.
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_humanize($str): string
    {
        $str = \str_replace(
            [
                '_id',
                '_',
            ],
            [
                '',
                ' ',
            ],
            $str
        );

        return self::ucfirst(\trim($str));
    }

    /**
     * Check if the string ends with the given substring, case-insensitive.
     *
     * EXAMPLE: <code>
     * UTF8::str_iends_with('BeginMiddleΚόσμε', 'Κόσμε'); // true
     * UTF8::str_iends_with('BeginMiddleΚόσμε', 'κόσμε'); // true
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function str_iends_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($haystack === '') {
            return false;
        }

        return self::strcasecmp(\substr($haystack, -\strlen($needle)), $needle) === 0;
    }

    /**
     * Returns true if the string ends with any of $substrings, false otherwise.
     *
     * - case-insensitive
     *
     * @param string   $str        <p>The input string.</p>
     * @param string[] $substrings <p>Substrings to look for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str ends with $substring.</p>
     */
    public static function str_iends_with_any(string $str, array $substrings): bool
    {
        if ($substrings === []) {
            return false;
        }

        foreach ($substrings as &$substring) {
            if (self::str_iends_with($str, $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Inserts $substring into the string at the $index provided.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $substring <p>String to be inserted.</p>
     * @param int    $index     <p>The index at which to insert the substring.</p>
     * @param string $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_insert(
        string $str,
        string $substring,
        int $index,
        string $encoding = self::UTF8
    ): string {
        if ($encoding === self::UTF8) {
            $len = (int) \mb_strlen($str);
            if ($index > $len) {
                return $str;
            }

            /** @noinspection UnnecessaryCastingInspection */
            return (string) \mb_substr($str, 0, $index) .
                   $substring .
                   (string) \mb_substr($str, $index, $len);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        $len = (int) self::strlen($str, $encoding);
        if ($index > $len) {
            return $str;
        }

        return ((string) self::substr($str, 0, $index, $encoding)) .
               $substring .
               ((string) self::substr($str, $index, $len, $encoding));
    }

    /**
     * Case-insensitive and UTF-8 safe version of <function>str_replace</function>.
     *
     * EXAMPLE: <code>
     * UTF8::str_ireplace('lIzÆ', 'lise', 'Iñtërnâtiônàlizætiøn'); // 'Iñtërnâtiônàlisetiøn'
     * </code>
     *
     * @see http://php.net/manual/en/function.str-ireplace.php
     *
     * @param string|string[] $search      <p>
     *                                     Every replacement with search array is
     *                                     performed on the result of previous replacement.
     *                                     </p>
     * @param string|string[] $replacement <p>The replacement.</p>
     * @param string|string[] $subject     <p>
     *                                     If subject is an array, then the search and
     *                                     replace is performed with every entry of
     *                                     subject, and the return value is an array as
     *                                     well.
     *                                     </p>
     * @param int             $count       [optional] <p>
     *                                     The number of matched and replaced needles will
     *                                     be returned in count which is passed by
     *                                     reference.
     *                                     </p>
     *
     * @psalm-pure
     *
     * @return string|string[]
     *                         <p>A string or an array of replacements.</p>
     *
     * @template TStrIReplaceSubject
     * @phpstan-param TStrIReplaceSubject $subject
     * @phpstan-return TStrIReplaceSubject
     */
    public static function str_ireplace($search, $replacement, $subject, &$count = null)
    {
        $search = (array) $search;

        /** @noinspection AlterInForeachInspection */
        foreach ($search as &$s) {
            $s = (string) $s;
            if ($s === '') {
                $s = '/^(?<=.)$/';
            } else {
                $s = '/' . \preg_quote($s, '/') . '/ui';
            }
        }

        // fallback
        /** @phpstan-ignore-next-line - only a fallback for PHP8 */
        if ($replacement === null) {
            $replacement = '';
        }
        /** @phpstan-ignore-next-line - only a fallback for PHP8 */
        if ($subject === null) {
            $subject = '';
        }

        /**
         * @psalm-suppress PossiblyNullArgument
         * @phpstan-var TStrIReplaceSubject $subject
         */
        $subject = \preg_replace($search, $replacement, $subject, -1, $count);

        return $subject;
    }

    /**
     * Replaces $search from the beginning of string with $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $search      <p>The string to search for.</p>
     * @param string $replacement <p>The replacement.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string after the replacement.</p>
     */
    public static function str_ireplace_beginning(string $str, string $search, string $replacement): string
    {
        if ($str === '') {
            if ($replacement === '') {
                return '';
            }

            if ($search === '') {
                return $replacement;
            }
        }

        if ($search === '') {
            return $str . $replacement;
        }

        $searchLength = \strlen($search);
        if (\strncasecmp($str, $search, $searchLength) === 0) {
            return $replacement . \substr($str, $searchLength);
        }

        return $str;
    }

    /**
     * Replaces $search from the ending of string with $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $search      <p>The string to search for.</p>
     * @param string $replacement <p>The replacement.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string after the replacement.</p>
     */
    public static function str_ireplace_ending(string $str, string $search, string $replacement): string
    {
        if ($str === '') {
            if ($replacement === '') {
                return '';
            }

            if ($search === '') {
                return $replacement;
            }
        }

        if ($search === '') {
            return $str . $replacement;
        }

        if (\stripos($str, $search, \strlen($str) - \strlen($search)) !== false) {
            $str = \substr($str, 0, -\strlen($search)) . $replacement;
        }

        return $str;
    }

    /**
     * Check if the string starts with the given substring, case-insensitive.
     *
     * EXAMPLE: <code>
     * UTF8::str_istarts_with('ΚόσμεMiddleEnd', 'Κόσμε'); // true
     * UTF8::str_istarts_with('ΚόσμεMiddleEnd', 'κόσμε'); // true
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function str_istarts_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($haystack === '') {
            return false;
        }

        return self::stripos($haystack, $needle) === 0;
    }

    /**
     * Returns true if the string begins with any of $substrings, false otherwise.
     *
     * - case-insensitive
     *
     * @param string   $str        <p>The input string.</p>
     * @param scalar[] $substrings <p>Substrings to look for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str starts with $substring.</p>
     */
    public static function str_istarts_with_any(string $str, array $substrings): bool
    {
        if ($str === '') {
            return false;
        }

        if ($substrings === []) {
            return false;
        }

        foreach ($substrings as &$substring) {
            if (self::str_istarts_with($str, (string) $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the substring after the first occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_after_first_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        $offset = self::stripos($str, $separator);
        if ($offset === false) {
            return '';
        }

        if ($encoding === self::UTF8) {
            return (string) \mb_substr(
                $str,
                $offset + (int) \mb_strlen($separator)
            );
        }

        return (string) self::substr(
            $str,
            $offset + (int) self::strlen($separator, $encoding),
            null,
            $encoding
        );
    }

    /**
     * Gets the substring after the last occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_after_last_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        $offset = self::strripos($str, $separator);
        if ($offset === false) {
            return '';
        }

        if ($encoding === self::UTF8) {
            return (string) \mb_substr(
                $str,
                $offset + (int) self::strlen($separator)
            );
        }

        return (string) self::substr(
            $str,
            $offset + (int) self::strlen($separator, $encoding),
            null,
            $encoding
        );
    }

    /**
     * Gets the substring before the first occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_before_first_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        $offset = self::stripos($str, $separator);
        if ($offset === false) {
            return '';
        }

        if ($encoding === self::UTF8) {
            return (string) \mb_substr($str, 0, $offset);
        }

        return (string) self::substr($str, 0, $offset, $encoding);
    }

    /**
     * Gets the substring before the last occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_before_last_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $offset = \mb_strripos($str, $separator);
            if ($offset === false) {
                return '';
            }

            return (string) \mb_substr($str, 0, $offset);
        }

        $offset = self::strripos($str, $separator, 0, $encoding);
        if ($offset === false) {
            return '';
        }

        return (string) self::substr($str, 0, $offset, $encoding);
    }

    /**
     * Gets the substring after (or before via "$before_needle") the first occurrence of the "$needle".
     *
     * @param string $str           <p>The input string.</p>
     * @param string $needle        <p>The string to look for.</p>
     * @param bool   $before_needle [optional] <p>Default: false</p>
     * @param string $encoding      [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_first(
        string $str,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8
    ): string {
        if (
            $needle === ''
            ||
            $str === ''
        ) {
            return '';
        }

        $part = self::stristr(
            $str,
            $needle,
            $before_needle,
            $encoding
        );
        if ($part === false) {
            return '';
        }

        return $part;
    }

    /**
     * Gets the substring after (or before via "$before_needle") the last occurrence of the "$needle".
     *
     * @param string $str           <p>The input string.</p>
     * @param string $needle        <p>The string to look for.</p>
     * @param bool   $before_needle [optional] <p>Default: false</p>
     * @param string $encoding      [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_isubstr_last(
        string $str,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8
    ): string {
        if (
            $needle === ''
            ||
            $str === ''
        ) {
            return '';
        }

        $part = self::strrichr(
            $str,
            $needle,
            $before_needle,
            $encoding
        );
        if ($part === false) {
            return '';
        }

        return $part;
    }

    /**
     * Returns the last $n characters of the string.
     *
     * @param string $str      <p>The input string.</p>
     * @param int    $n        <p>Number of characters to retrieve from the end.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_last_char(
        string $str,
        int $n = 1,
        string $encoding = self::UTF8
    ): string {
        if ($str === '' || $n <= 0) {
            return '';
        }

        if ($encoding === self::UTF8) {
            return (string) \mb_substr($str, -$n);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        return (string) self::substr($str, -$n, null, $encoding);
    }

    /**
     * Limit the number of characters in a string.
     *
     * @param string      $str        <p>The input string.</p>
     * @param int<1, max> $length     [optional] <p>Default: 100</p>
     * @param string      $str_add_on [optional] <p>Default: …</p>
     * @param string      $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_limit(
        string $str,
        int $length = 100,
        string $str_add_on = '…',
        string $encoding = self::UTF8
    ): string {
        if (
            $str === ''
            ||
            /* @phpstan-ignore-next-line | we do not trust the phpdoc check */
            $length <= 0
        ) {
            return '';
        }

        if ($encoding === self::UTF8) {
            if ((int) \mb_strlen($str) <= $length) {
                return $str;
            }

            /** @noinspection UnnecessaryCastingInspection */
            return (string) \mb_substr($str, 0, $length - (int) self::strlen($str_add_on)) . $str_add_on;
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ((int) self::strlen($str, $encoding) <= $length) {
            return $str;
        }

        return ((string) self::substr($str, 0, $length - (int) self::strlen($str_add_on), $encoding)) . $str_add_on;
    }

    /**
     * Limit the number of characters in a string in bytes.
     *
     * @param string      $str        <p>The input string.</p>
     * @param int<1, max> $length     [optional] <p>Default: 100</p>
     * @param string      $str_add_on [optional] <p>Default: ...</p>
     * @param string      $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_limit_in_byte(
        string $str,
        int $length = 100,
        string $str_add_on = '...',
        string $encoding = self::UTF8
    ): string {
        if (
            $str === ''
            ||
            /* @phpstan-ignore-next-line | we do not trust the phpdoc check */
            $length <= 0
        ) {
            return '';
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ((int) self::strlen_in_byte($str, $encoding) <= $length) {
            return $str;
        }

        return ((string) self::substr_in_byte($str, 0, $length - (int) self::strlen_in_byte($str_add_on), $encoding)) . $str_add_on;
    }

    /**
     * Limit the number of characters in a string, but also after the next word.
     *
     * EXAMPLE: <code>UTF8::str_limit_after_word('fòô bàř fòô', 8, ''); // 'fòô bàř'</code>
     *
     * @param string      $str        <p>The input string.</p>
     * @param int<1, max> $length     [optional] <p>Default: 100</p>
     * @param string      $str_add_on [optional] <p>Default: …</p>
     * @param string      $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_limit_after_word(
        string $str,
        int $length = 100,
        string $str_add_on = '…',
        string $encoding = self::UTF8
    ): string {
        if (
            $str === ''
            ||
            /* @phpstan-ignore-next-line | we do not trust the phpdoc check */
            $length <= 0
        ) {
            return '';
        }

        if ($encoding === self::UTF8) {
            if ((int) \mb_strlen($str) <= $length) {
                return $str;
            }

            if (\mb_substr($str, $length - 1, 1) === ' ') {
                return ((string) \mb_substr($str, 0, $length - 1)) . $str_add_on;
            }

            $str = \mb_substr($str, 0, $length);

            $array = \explode(' ', $str, -1);
            $new_str = \implode(' ', $array);

            if ($new_str === '') {
                return ((string) \mb_substr($str, 0, $length - 1)) . $str_add_on;
            }
        } else {
            if ((int) self::strlen($str, $encoding) <= $length) {
                return $str;
            }

            if (self::substr($str, $length - 1, 1, $encoding) === ' ') {
                return ((string) self::substr($str, 0, $length - 1, $encoding)) . $str_add_on;
            }

            /** @noinspection CallableParameterUseCaseInTypeContextInspection - FP */
            $str = self::substr($str, 0, $length, $encoding);
            if ($str === false) {
                return '' . $str_add_on;
            }

            $array = \explode(' ', $str, -1);
            $new_str = \implode(' ', $array);

            if ($new_str === '') {
                return ((string) self::substr($str, 0, $length - 1, $encoding)) . $str_add_on;
            }
        }

        return $new_str . $str_add_on;
    }

    /**
     * Returns the longest common prefix between the $str1 and $str2.
     *
     * @param string $str1     <p>The input sting.</p>
     * @param string $str2     <p>Second string for comparison.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_longest_common_prefix(
        string $str1,
        string $str2,
        string $encoding = self::UTF8
    ): string {
        // init
        $longest_common_prefix = '';

        if ($encoding === self::UTF8) {
            $max_length = (int) \min(
                \mb_strlen($str1),
                \mb_strlen($str2)
            );

            for ($i = 0; $i < $max_length; ++$i) {
                $char = \mb_substr($str1, $i, 1);

                if (
                    $char !== false /* @phpstan-ignore-line | old polyfill will return false, or? */
                    &&
                    $char === \mb_substr($str2, $i, 1)
                ) {
                    $longest_common_prefix .= $char;
                } else {
                    break;
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $max_length = (int) \min(
                self::strlen($str1, $encoding),
                self::strlen($str2, $encoding)
            );

            for ($i = 0; $i < $max_length; ++$i) {
                $char = self::substr($str1, $i, 1, $encoding);

                if (
                    $char !== false
                    &&
                    $char === self::substr($str2, $i, 1, $encoding)
                ) {
                    $longest_common_prefix .= $char;
                } else {
                    break;
                }
            }
        }

        return $longest_common_prefix;
    }

    /**
     * Returns the longest common substring between the $str1 and $str2.
     * In the case of ties, it returns that which occurs first.
     *
     * @param string $str1
     * @param string $str2     <p>Second string for comparison.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with its $str being the longest common substring.</p>
     */
    public static function str_longest_common_substring(
        string $str1,
        string $str2,
        string $encoding = self::UTF8
    ): string {
        if ($str1 === '' || $str2 === '') {
            return '';
        }

        // Uses dynamic programming to solve
        // http://en.wikipedia.org/wiki/Longest_common_substring_problem

        if ($encoding === self::UTF8) {
            $str_length = (int) \mb_strlen($str1);
            $other_length = (int) \mb_strlen($str2);
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $str_length = (int) self::strlen($str1, $encoding);
            $other_length = (int) self::strlen($str2, $encoding);
        }

        // Return if either string is empty
        if ($str_length === 0 || $other_length === 0) {
            return '';
        }

        $len = 0;
        $end = 0;
        $table = \array_fill(
            0,
            $str_length + 1,
            \array_fill(0, $other_length + 1, 0)
        );

        if ($encoding === self::UTF8) {
            for ($i = 1; $i <= $str_length; ++$i) {
                for ($j = 1; $j <= $other_length; ++$j) {
                    $str_char = \mb_substr($str1, $i - 1, 1);
                    $other_char = \mb_substr($str2, $j - 1, 1);

                    if ($str_char === $other_char) {
                        $table[$i][$j] = $table[$i - 1][$j - 1] + 1;
                        if ($table[$i][$j] > $len) {
                            $len = $table[$i][$j];
                            $end = $i;
                        }
                    } else {
                        $table[$i][$j] = 0;
                    }
                }
            }
        } else {
            for ($i = 1; $i <= $str_length; ++$i) {
                for ($j = 1; $j <= $other_length; ++$j) {
                    $str_char = self::substr($str1, $i - 1, 1, $encoding);
                    $other_char = self::substr($str2, $j - 1, 1, $encoding);

                    if ($str_char === $other_char) {
                        $table[$i][$j] = $table[$i - 1][$j - 1] + 1;
                        if ($table[$i][$j] > $len) {
                            $len = $table[$i][$j];
                            $end = $i;
                        }
                    } else {
                        $table[$i][$j] = 0;
                    }
                }
            }
        }

        if ($encoding === self::UTF8) {
            return (string) \mb_substr($str1, $end - $len, $len);
        }

        return (string) self::substr($str1, $end - $len, $len, $encoding);
    }

    /**
     * Returns the longest common suffix between the $str1 and $str2.
     *
     * @param string $str1
     * @param string $str2     <p>Second string for comparison.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_longest_common_suffix(
        string $str1,
        string $str2,
        string $encoding = self::UTF8
    ): string {
        if ($str1 === '' || $str2 === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $max_length = (int) \min(
                \mb_strlen($str1, $encoding),
                \mb_strlen($str2, $encoding)
            );

            $longest_common_suffix = '';
            for ($i = 1; $i <= $max_length; ++$i) {
                $char = \mb_substr($str1, -$i, 1);

                if (
                    $char !== false /* @phpstan-ignore-line | old polyfill will return false, or? */
                    &&
                    $char === \mb_substr($str2, -$i, 1)
                ) {
                    $longest_common_suffix = $char . $longest_common_suffix;
                } else {
                    break;
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $max_length = (int) \min(
                self::strlen($str1, $encoding),
                self::strlen($str2, $encoding)
            );

            $longest_common_suffix = '';
            for ($i = 1; $i <= $max_length; ++$i) {
                $char = self::substr($str1, -$i, 1, $encoding);

                if (
                    $char !== false
                    &&
                    $char === self::substr($str2, -$i, 1, $encoding)
                ) {
                    $longest_common_suffix = $char . $longest_common_suffix;
                } else {
                    break;
                }
            }
        }

        return $longest_common_suffix;
    }

    /**
     * Returns true if $str matches the supplied pattern, false otherwise.
     *
     * @param string $str     <p>The input string.</p>
     * @param string $pattern <p>Regex pattern to match against.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str matches the pattern.</p>
     */
    public static function str_matches_pattern(string $str, string $pattern): bool
    {
        return (bool) \preg_match('/' . $pattern . '/u', $str);
    }

    /**
     * Returns whether or not a character exists at an index. Offsets may be
     * negative to count from the last character in the string. Implements
     * part of the ArrayAccess interface.
     *
     * @param string $str      <p>The input string.</p>
     * @param int    $offset   <p>The index to check.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not the index exists.</p>
     */
    public static function str_offset_exists(string $str, int $offset, string $encoding = self::UTF8): bool
    {
        // init
        $length = (int) self::strlen($str, $encoding);

        if ($offset >= 0) {
            return $length > $offset;
        }

        return $length >= \abs($offset);
    }

    /**
     * Returns the character at the given index. Offsets may be negative to
     * count from the last character in the string. Implements part of the
     * ArrayAccess interface, and throws an OutOfBoundsException if the index
     * does not exist.
     *
     * @param string      $str      <p>The input string.</p>
     * @param int<1, max> $index    <p>The <strong>index</strong> from which to retrieve the char.</p>
     * @param string      $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @throws \OutOfBoundsException if the positive or negative offset does not exist
     *
     * @return string
     *                <p>The character at the specified index.</p>
     *
     * @psalm-pure
     */
    public static function str_offset_get(string $str, int $index, string $encoding = self::UTF8): string
    {
        // init
        $length = (int) self::strlen($str);

        if (
            /* @phpstan-ignore-next-line | we do not trust the phpdoc check */
            ($index >= 0 && $length <= $index)
            ||
            $length < \abs($index)
        ) {
            throw new \OutOfBoundsException('No character exists at the index');
        }

        return self::charAt($str, $index, $encoding);
    }

    /**
     * Pad a UTF-8 string to a given length with another string.
     *
     * EXAMPLE: <code>UTF8::str_pad('中文空白', 10, '_', STR_PAD_BOTH); // '___中文空白___'</code>
     *
     * @param string     $str        <p>The input string.</p>
     * @param int        $pad_length <p>The length of return string.</p>
     * @param string     $pad_string [optional] <p>String to use for padding the input string.</p>
     * @param int|string $pad_type   [optional] <p>
     *                               Can be <strong>STR_PAD_RIGHT</strong> (default), [or string "right"]<br>
     *                               <strong>STR_PAD_LEFT</strong> [or string "left"] or<br>
     *                               <strong>STR_PAD_BOTH</strong> [or string "both"]
     *                               </p>
     * @param string     $encoding   [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Returns the padded string.</p>
     */
    public static function str_pad(
        string $str,
        int $pad_length,
        string $pad_string = ' ',
        $pad_type = \STR_PAD_RIGHT,
        string $encoding = self::UTF8
    ): string {
        if ($pad_length === 0 || $pad_string === '') {
            return $str;
        }

        if ($pad_type !== (int) $pad_type) {
            if ($pad_type === 'left') {
                $pad_type = \STR_PAD_LEFT;
            } elseif ($pad_type === 'right') {
                $pad_type = \STR_PAD_RIGHT;
            } elseif ($pad_type === 'both') {
                $pad_type = \STR_PAD_BOTH;
            } else {
                throw new \InvalidArgumentException(
                    'Pad expects $pad_type to be "STR_PAD_*" or ' . "to be one of 'left', 'right' or 'both'"
                );
            }
        }

        if ($encoding === self::UTF8) {
            $str_length = (int) \mb_strlen($str);

            if ($pad_length >= $str_length) {
                switch ($pad_type) {
                    case \STR_PAD_LEFT:
                        $ps_length = (int) \mb_strlen($pad_string);

                        $diff = ($pad_length - $str_length);

                        $pre = (string) \mb_substr(
                            \str_repeat($pad_string, (int) \ceil($diff / $ps_length)),
                            0,
                            $diff
                        );
                        $post = '';

                        break;

                    case \STR_PAD_BOTH:
                        $diff = ($pad_length - $str_length);

                        $ps_length_left = (int) \floor($diff / 2);

                        $ps_length_right = (int) \ceil($diff / 2);

                        $pre = (string) \mb_substr(
                            \str_repeat($pad_string, $ps_length_left),
                            0,
                            $ps_length_left
                        );
                        $post = (string) \mb_substr(
                            \str_repeat($pad_string, $ps_length_right),
                            0,
                            $ps_length_right
                        );

                        break;

                    case \STR_PAD_RIGHT:
                    default:
                        $ps_length = (int) \mb_strlen($pad_string);

                        $diff = ($pad_length - $str_length);

                        $post = (string) \mb_substr(
                            \str_repeat($pad_string, (int) \ceil($diff / $ps_length)),
                            0,
                            $diff
                        );
                        $pre = '';
                }

                return $pre . $str . $post;
            }

            return $str;
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        $str_length = (int) self::strlen($str, $encoding);

        if ($pad_length >= $str_length) {
            switch ($pad_type) {
                case \STR_PAD_LEFT:
                    $ps_length = (int) self::strlen($pad_string, $encoding);

                    $diff = ($pad_length - $str_length);

                    $pre = (string) self::substr(
                        \str_repeat($pad_string, (int) \ceil($diff / $ps_length)),
                        0,
                        $diff,
                        $encoding
                    );
                    $post = '';

                    break;

                case \STR_PAD_BOTH:
                    $diff = ($pad_length - $str_length);

                    $ps_length_left = (int) \floor($diff / 2);

                    $ps_length_right = (int) \ceil($diff / 2);

                    $pre = (string) self::substr(
                        \str_repeat($pad_string, $ps_length_left),
                        0,
                        $ps_length_left,
                        $encoding
                    );
                    $post = (string) self::substr(
                        \str_repeat($pad_string, $ps_length_right),
                        0,
                        $ps_length_right,
                        $encoding
                    );

                    break;

                case \STR_PAD_RIGHT:
                default:
                    $ps_length = (int) self::strlen($pad_string, $encoding);

                    $diff = ($pad_length - $str_length);

                    $post = (string) self::substr(
                        \str_repeat($pad_string, (int) \ceil($diff / $ps_length)),
                        0,
                        $diff,
                        $encoding
                    );
                    $pre = '';
            }

            return $pre . $str . $post;
        }

        return $str;
    }

    /**
     * Returns a new string of a given length such that both sides of the
     * string are padded. Alias for "UTF8::str_pad()" with a $pad_type of 'both'.
     *
     * @param string $str
     * @param int    $length   <p>Desired string length after padding.</p>
     * @param string $pad_str  [optional] <p>String used to pad, defaults to space. Default: ' '</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string with padding applied.</p>
     */
    public static function str_pad_both(
        string $str,
        int $length,
        string $pad_str = ' ',
        string $encoding = self::UTF8
    ): string {
        return self::str_pad(
            $str,
            $length,
            $pad_str,
            \STR_PAD_BOTH,
            $encoding
        );
    }

    /**
     * Returns a new string of a given length such that the beginning of the
     * string is padded. Alias for "UTF8::str_pad()" with a $pad_type of 'left'.
     *
     * @param string $str
     * @param int    $length   <p>Desired string length after padding.</p>
     * @param string $pad_str  [optional] <p>String used to pad, defaults to space. Default: ' '</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string with left padding.</p>
     */
    public static function str_pad_left(
        string $str,
        int $length,
        string $pad_str = ' ',
        string $encoding = self::UTF8
    ): string {
        return self::str_pad(
            $str,
            $length,
            $pad_str,
            \STR_PAD_LEFT,
            $encoding
        );
    }

    /**
     * Returns a new string of a given length such that the end of the string
     * is padded. Alias for "UTF8::str_pad()" with a $pad_type of 'right'.
     *
     * @param string $str
     * @param int    $length   <p>Desired string length after padding.</p>
     * @param string $pad_str  [optional] <p>String used to pad, defaults to space. Default: ' '</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string with right padding.</p>
     */
    public static function str_pad_right(
        string $str,
        int $length,
        string $pad_str = ' ',
        string $encoding = self::UTF8
    ): string {
        return self::str_pad(
            $str,
            $length,
            $pad_str,
            \STR_PAD_RIGHT,
            $encoding
        );
    }

    /**
     * Repeat a string.
     *
     * EXAMPLE: <code>UTF8::str_repeat("°~\xf0\x90\x28\xbc", 2); // '°~ð(¼°~ð(¼'</code>
     *
     * @param string      $str   <p>
     *                           The string to be repeated.
     *                           </p>
     * @param int<1, max> $multiplier <p>
     *                           Number of time the input string should be
     *                           repeated.
     *                           </p>
     *                           <p>
     *                           multiplier has to be greater than or equal to 0.
     *                           If the multiplier is set to 0, the function
     *                           will return an empty string.
     *                           </p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The repeated string.</p>
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_repeat(string $str, int $multiplier): string
    {
        $str = self::filter($str);

        return \str_repeat($str, $multiplier);
    }

    /**
     * INFO: This is only a wrapper for "str_replace()"  -> the original functions is already UTF-8 safe.
     *
     * Replace all occurrences of the search string with the replacement string
     *
     * @see http://php.net/manual/en/function.str-replace.php
     *
     * @param string|string[] $search  <p>
     *                                 The value being searched for, otherwise known as the needle.
     *                                 An array may be used to designate multiple needles.
     *                                 </p>
     * @param string|string[] $replace <p>
     *                                 The replacement value that replaces found search
     *                                 values. An array may be used to designate multiple replacements.
     *                                 </p>
     * @param string|string[] $subject <p>
     *                                 The string or array of strings being searched and replaced on,
     *                                 otherwise known as the haystack.
     *                                 </p>
     *                                 <p>
     *                                 If subject is an array, then the search and
     *                                 replace is performed with every entry of
     *                                 subject, and the return value is an array as
     *                                 well.
     *                                 </p>
     * @param int|null        $count   [optional] <p>
     *                                 If passed, this will hold the number of matched and replaced needles.
     *                                 </p>
     *
     * @psalm-pure
     *
     * @return string|string[]
     *                         <p>This function returns a string or an array with the replaced values.</p>
     *
     * @template TStrReplaceSubject
     * @phpstan-param TStrReplaceSubject $subject
     * @phpstan-return TStrReplaceSubject
     *
     * @deprecated please use \str_replace() instead
     */
    public static function str_replace(
        $search,
        $replace,
        $subject,
        ?int &$count = null
    ) {
        /**
         * @psalm-suppress PossiblyNullArgument
         * @phpstan-var TStrReplaceSubject $return;
         */
        $return = \str_replace(
            $search,
            $replace,
            $subject,
            $count
        );

        return $return;
    }

    /**
     * Replaces $search from the beginning of string with $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $search      <p>The string to search for.</p>
     * @param string $replacement <p>The replacement.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string after the replacements.</p>
     */
    public static function str_replace_beginning(
        string $str,
        string $search,
        string $replacement
    ): string {
        if ($str === '') {
            if ($replacement === '') {
                return '';
            }

            if ($search === '') {
                return $replacement;
            }
        }

        if ($search === '') {
            return $str . $replacement;
        }

        $searchLength = \strlen($search);
        if (\strncmp($str, $search, $searchLength) === 0) {
            return $replacement . \substr($str, $searchLength);
        }

        return $str;
    }

    /**
     * Replaces $search from the ending of string with $replacement.
     *
     * @param string $str         <p>The input string.</p>
     * @param string $search      <p>The string to search for.</p>
     * @param string $replacement <p>The replacement.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string after the replacements.</p>
     */
    public static function str_replace_ending(
        string $str,
        string $search,
        string $replacement
    ): string {
        if ($str === '') {
            if ($replacement === '') {
                return '';
            }

            if ($search === '') {
                return $replacement;
            }
        }

        if ($search === '') {
            return $str . $replacement;
        }

        if (\strpos($str, $search, \strlen($str) - \strlen($search)) !== false) {
            $str = \substr($str, 0, -\strlen($search)) . $replacement;
        }

        return $str;
    }

    /**
     * Replace the first "$search"-term with the "$replace"-term.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @psalm-pure
     *
     * @return string
     *
     * @psalm-suppress InvalidReturnType
     */
    public static function str_replace_first(
        string $search,
        string $replace,
        string $subject
    ): string {
        $pos = self::strpos($subject, $search);

        if ($pos !== false) {
            /**
             * @psalm-suppress InvalidReturnStatement
             */
            return self::substr_replace(
                $subject,
                $replace,
                $pos,
                (int) self::strlen($search)
            );
        }

        return $subject;
    }

    /**
     * Replace the last "$search"-term with the "$replace"-term.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @psalm-pure
     *
     * @return string
     *
     * @psalm-suppress InvalidReturnType
     */
    public static function str_replace_last(
        string $search,
        string $replace,
        string $subject
    ): string {
        $pos = self::strrpos($subject, $search);
        if ($pos !== false) {
            /**
             * @psalm-suppress InvalidReturnStatement
             */
            return self::substr_replace(
                $subject,
                $replace,
                $pos,
                (int) self::strlen($search)
            );
        }

        return $subject;
    }

    /**
     * Shuffles all the characters in the string.
     *
     * INFO: uses random algorithm which is weak for cryptography purposes
     *
     * EXAMPLE: <code>UTF8::str_shuffle('fòô bàř fòô'); // 'àòôřb ffòô '</code>
     *
     * @param string $str      <p>The input string</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @return string
     *                <p>The shuffled string.</p>
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function str_shuffle(string $str, string $encoding = self::UTF8): string
    {
        if ($encoding === self::UTF8) {
            $indexes = \range(0, (int) \mb_strlen($str) - 1);
            \shuffle($indexes);

            // init
            $shuffled_str = '';

            foreach ($indexes as &$i) {
                $tmp_sub_str = \mb_substr($str, $i, 1);
                if ($tmp_sub_str !== false) { /* @phpstan-ignore-line | old polyfill will return false, or? */
                    $shuffled_str .= $tmp_sub_str;
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $indexes = \range(0, (int) self::strlen($str, $encoding) - 1);
            \shuffle($indexes);

            // init
            $shuffled_str = '';

            foreach ($indexes as &$i) {
                $tmp_sub_str = self::substr($str, $i, 1, $encoding);
                if ($tmp_sub_str !== false) {
                    $shuffled_str .= $tmp_sub_str;
                }
            }
        }

        return $shuffled_str;
    }

    /**
     * Returns the substring beginning at $start, and up to, but not including
     * the index specified by $end. If $end is omitted, the function extracts
     * the remaining string. If $end is negative, it is computed from the end
     * of the string.
     *
     * @param string   $str
     * @param int      $start    <p>Initial index from which to begin extraction.</p>
     * @param int|null $end      [optional] <p>Index at which to end extraction. Default: null</p>
     * @param string   $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The extracted substring.</p><p>If <i>str</i> is shorter than <i>start</i>
     *                      characters long, <b>FALSE</b> will be returned.
     */
    public static function str_slice(
        string $str,
        int $start,
        ?int $end = null,
        string $encoding = self::UTF8
    ) {
        if ($encoding === self::UTF8) {
            if ($end === null) {
                $length = (int) \mb_strlen($str);
            } elseif ($end >= 0 && $end <= $start) {
                return '';
            } elseif ($end < 0) {
                $length = (int) \mb_strlen($str) + $end - $start;
            } else {
                $length = $end - $start;
            }

            return \mb_substr($str, $start, $length);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ($end === null) {
            $length = (int) self::strlen($str, $encoding);
        } elseif ($end >= 0 && $end <= $start) {
            return '';
        } elseif ($end < 0) {
            $length = (int) self::strlen($str, $encoding) + $end - $start;
        } else {
            $length = $end - $start;
        }

        return self::substr($str, $start, $length, $encoding);
    }

    /**
     * Convert a string to e.g.: "snake_case"
     *
     * @param string $str
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string in snake_case.</p>
     */
    public static function str_snakeize(string $str, string $encoding = self::UTF8): string
    {
        if ($str === '') {
            return '';
        }

        $str = \str_replace(
            '-',
            '_',
            self::normalizeWhitespace($str)
        );

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        $str = (string) \preg_replace_callback(
            '/([\\p{N}|\\p{Lu}])/u',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                $match = $matches[1];
                $match_int = (int) $match;

                if ((string) $match_int === $match) {
                    return '_' . $match . '_';
                }

                if ($encoding === self::UTF8) {
                    return '_' . \mb_strtolower($match);
                }

                return '_' . self::strtolower($match, $encoding);
            },
            $str
        );

        $str = (string) \preg_replace(
            [
                '/\\s+/u',        // convert spaces to "_"
                '/^\\s+|\\s+$/u', // trim leading & trailing spaces
                '/_+/',           // remove double "_"
            ],
            [
                '_',
                '',
                '_',
            ],
            $str
        );

        return \trim(\trim($str, '_')); // trim leading & trailing "_" + whitespace
    }

    /**
     * Sort all characters according to code points.
     *
     * EXAMPLE: <code>UTF8::str_sort('  -ABC-中文空白-  '); // '    ---ABC中文白空'</code>
     *
     * @param string $str    <p>A UTF-8 string.</p>
     * @param bool   $unique <p>Sort unique. If <strong>true</strong>, repeated characters are ignored.</p>
     * @param bool   $desc   <p>If <strong>true</strong>, will sort characters in reverse code point order.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string of sorted characters.</p>
     */
    public static function str_sort(string $str, bool $unique = false, bool $desc = false): string
    {
        /** @var int[] $array */
        $array = self::codepoints($str);

        if ($unique) {
            $array = \array_flip(\array_flip($array));
        }

        if ($desc) {
            \arsort($array);
        } else {
            \asort($array);
        }

        return self::string($array);
    }

    /**
     * Converts a string to an array of Unicode characters.
     *
     * Example: UTF8::strSplitArray(['中文空白', 'test'], 2); // [['中文', '空白'], ['te', 'st']]
     *
     * @param array<int|string> $input The array of strings or integers to split into sub-arrays.
     * @param int<1, max> $length The max character length of each array element (default: 1).
     * @param bool $cleanUtf8 Whether to remove non-UTF-8 characters (default: false).
     * @param bool $tryToUseMbFunctions Whether to use "mb_substr" (default: true).
     *
     * @psalm-pure
     *
     * @return array<array<string>> An array containing chunks of the input strings.
     */
    public static function strSplitArray(
        array $input,
        int $length = 1,
        bool $cleanUtf8 = false,
        bool $tryToUseMbFunctions = true
    ): array {
        foreach ($input as &$value) {
            $value = self::strSplit(
                $value,
                $length,
                $cleanUtf8,
                $tryToUseMbFunctions
            );
        }

        return $input;
    }

    /**
     * Converts a string to an array of Unicode characters.
     *
     * Example: UTF8::strSplit('中文空白'); // ['中', '文', '空', '白']
     *
     * @param string|int $str The string or integer to split into an array.
     * @param int<1, max> $length The max character length of each array element (default: 1).
     * @param bool $cleanUtf8 Whether to remove non-UTF-8 characters (default: false).
     * @param bool $tryToUseMbFunctions Whether to use "mb_substr" (default: true).
     *
     * @psalm-pure
     *
     * @return list<string> An array containing chunks of characters from the input.
     */
    public static function strSplit(
        $str,
        int $length = 1,
        bool $cleanUtf8 = false,
        bool $tryToUseMbFunctions = true
    ): array {
        // If the length is invalid, return an empty array
        if ($length <= 0) {
            return [];
        }

        // Handle array inputs
        if (is_array($str)) {
            return self::strSplitArray(
                $str,
                $length,
                $cleanUtf8,
                $tryToUseMbFunctions
            );
        }

        // Convert to string and check for empty
        $str = (string) $str;
        if ($str === '') {
            return [];
        }

        // Clean the string if needed
        if ($cleanUtf8) {
            $str = self::clean($str);
        }

        // Use multibyte functions if available and enabled
        if ($tryToUseMbFunctions && self::$SUPPORT[self::FEATURE_MBSTRING]) {
            if (function_exists('mb_strSplit')) {
                try {
                    // Attempt to use mb_strSplit
                    return mb_str_split($str, $length);
                } catch (Error $e) {
                    // Fallback if mb_strSplit() fails
                }
            }

            return self::splitUsingMbFunctions($str, $length);
        }

        // Fallback to regular PCRE if mbstring is not supported
        return self::splitUsingPcre($str, $length);
    }

    /**
     * Split the string using multibyte functions.
     *
     * @param string $str The input string.
     * @param int $length The length of each chunk.
     *
     * @return array<string> An array of string chunks.
     */
    private static function splitUsingMbFunctions(string $str, int $length): array {
        $iMax = mb_strlen($str);
        $ret = [];

        if ($iMax <= 127) {
            // For small strings, we can loop through each character.
            for ($i = 0; $i < $iMax; ++$i) {
                $ret[] = mb_substr($str, $i, 1);
            }
        } else {
            // For large strings, split them using a regular expression.
            preg_match_all('/./us', $str, $ret);
            $ret = $ret[0] ?? [];  // Ensure ret is an array of characters.
        }

        // If length > 1, chunk the result into smaller arrays of specified length
        return $length > 1 ? array_map(
            static fn($item) => implode('', $item),
            array_chunk($ret, $length)
        ) : $ret;
    }

    /**
     * Split the string using PCRE (fallback method).
     *
     * @param string $str The input string.
     * @param int $length The length of each chunk.
     *
     * @return array<string> An array of string chunks.
     */
    private static function splitUsingPcre(string $str, int $length): array {
        preg_match_all('/./us', $str, $returnArray);

        return $length > 1 ? array_map(
            static fn($item) => implode('', $item),
            array_chunk($returnArray[0], $length)
        ) : $returnArray[0] ?? [];
    }

    /**
     * Splits the string with the provided regular expression, returning an
     * array of strings. An optional integer $limit will truncate the
     * results.
     *
     * @param string $str
     * @param string $pattern <p>The regex with which to split the string.</p>
     * @param int    $limit   [optional] <p>Maximum number of results to return. Default: -1 === no limit</p>
     *
     * @psalm-pure
     *
     * @return string[]
     *                  <p>An array of strings.</p>
     */
    public static function strSplit_pattern(string $str, string $pattern, int $limit = -1): array
    {
        if ($limit === 0) {
            return [];
        }

        if ($pattern === '') {
            return [$str];
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($limit >= 0) {
                $result_tmp = \mb_split($pattern, $str);
                if ($result_tmp === false) {
                    return [];
                }

                $result = [];
                foreach ($result_tmp as $item_tmp) {
                    if ($limit === 0) {
                        break;
                    }
                    --$limit;

                    $result[] = $item_tmp;
                }

                return $result;
            }

            $result = \mb_split($pattern, $str);
            if ($result === false) {
                return [];
            }

            return $result;
        }

        if ($limit > 0) {
            ++$limit;
        } else {
            $limit = -1;
        }

        $array = \preg_split('/' . \preg_quote($pattern, '/') . '/u', $str, $limit);
        if ($array === false) {
            return [];
        }

        if ($limit > 0 && \count($array) === $limit) {
            \array_pop($array);
        }

        return $array;
    }

    /**
     * Check if the string starts with the given substring.
     *
     * EXAMPLE: <code>
     * UTF8::str_starts_with('ΚόσμεMiddleEnd', 'Κόσμε'); // true
     * UTF8::str_starts_with('ΚόσμεMiddleEnd', 'κόσμε'); // false
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function str_starts_with(string $haystack, string $needle): bool
    {
        if ($needle === '') {
            return true;
        }

        if ($haystack === '') {
            return false;
        }

        if (\PHP_VERSION_ID >= 80000) {
            /** @phpstan-ignore-next-line - only for PHP8 */
            return \str_starts_with($haystack, $needle);
        }

        return \strncmp($haystack, $needle, \strlen($needle)) === 0;
    }

    /**
     * Returns true if the string begins with any of $substrings, false otherwise.
     *
     * - case-sensitive
     *
     * @param string   $str        <p>The input string.</p>
     * @param scalar[] $substrings <p>Substrings to look for.</p>
     *
     * @psalm-pure
     *
     * @return bool
     *              <p>Whether or not $str starts with $substring.</p>
     */
    public static function str_starts_with_any(string $str, array $substrings): bool
    {
        if ($str === '') {
            return false;
        }

        if ($substrings === []) {
            return false;
        }

        foreach ($substrings as &$substring) {
            if (self::str_starts_with($str, (string) $substring)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Gets the substring after the first occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_after_first_separator(string $str, string $separator, string $encoding = self::UTF8): string
    {
        if ($separator === '' || $str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $offset = \mb_strpos($str, $separator);
            if ($offset === false) {
                return '';
            }

            return (string) \mb_substr(
                $str,
                $offset + (int) \mb_strlen($separator)
            );
        }

        $offset = self::strpos($str, $separator, 0, $encoding);
        if ($offset === false) {
            return '';
        }

        return (string) \mb_substr(
            $str,
            $offset + (int) self::strlen($separator, $encoding),
            null,
            $encoding
        );
    }

    /**
     * Gets the substring after the last occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_after_last_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $offset = \mb_strrpos($str, $separator);
            if ($offset === false) {
                return '';
            }

            return (string) \mb_substr(
                $str,
                $offset + (int) \mb_strlen($separator)
            );
        }

        $offset = self::strrpos($str, $separator, 0, $encoding);
        if ($offset === false) {
            return '';
        }

        return (string) self::substr(
            $str,
            $offset + (int) self::strlen($separator, $encoding),
            null,
            $encoding
        );
    }

    /**
     * Gets the substring before the first occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_before_first_separator(
        string $str,
        string $separator,
        string $encoding = self::UTF8
    ): string {
        if ($separator === '' || $str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $offset = \mb_strpos($str, $separator);
            if ($offset === false) {
                return '';
            }

            return (string) \mb_substr(
                $str,
                0,
                $offset
            );
        }

        $offset = self::strpos($str, $separator, 0, $encoding);
        if ($offset === false) {
            return '';
        }

        return (string) self::substr(
            $str,
            0,
            $offset,
            $encoding
        );
    }

    /**
     * Gets the substring before the last occurrence of a separator.
     *
     * @param string $str       <p>The input string.</p>
     * @param string $separator <p>The string separator.</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_before_last_separator(string $str, string $separator, string $encoding = self::UTF8): string
    {
        if ($separator === '' || $str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            $offset = \mb_strrpos($str, $separator);
            if ($offset === false) {
                return '';
            }

            return (string) \mb_substr(
                $str,
                0,
                $offset
            );
        }

        $offset = self::strrpos($str, $separator, 0, $encoding);
        if ($offset === false) {
            return '';
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        return (string) self::substr(
            $str,
            0,
            $offset,
            $encoding
        );
    }

    /**
     * Gets the substring after (or before via "$before_needle") the first occurrence of the "$needle".
     *
     * @param string $str           <p>The input string.</p>
     * @param string $needle        <p>The string to look for.</p>
     * @param bool   $before_needle [optional] <p>Default: false</p>
     * @param string $encoding      [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_first(
        string $str,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8
    ): string {
        if ($str === '' || $needle === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            if ($before_needle) {
                $part = \mb_strstr(
                    $str,
                    $needle,
                    $before_needle
                );
            } else {
                $part = \mb_strstr(
                    $str,
                    $needle
                );
            }
        } else {
            $part = self::strstr(
                $str,
                $needle,
                $before_needle,
                $encoding
            );
        }

        return $part === false ? '' : $part;
    }

    /**
     * Gets the substring after (or before via "$before_needle") the last occurrence of the "$needle".
     *
     * @param string $str           <p>The input string.</p>
     * @param string $needle        <p>The string to look for.</p>
     * @param bool   $before_needle [optional] <p>Default: false</p>
     * @param string $encoding      [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function str_substr_last(
        string $str,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8
    ): string {
        if ($str === '' || $needle === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            if ($before_needle) {
                $part = \mb_strrchr(
                    $str,
                    $needle,
                    $before_needle
                );
            } else {
                $part = \mb_strrchr(
                    $str,
                    $needle
                );
            }
        } else {
            $part = self::strrchr(
                $str,
                $needle,
                $before_needle,
                $encoding
            );
        }

        return $part === false ? '' : $part;
    }

    /**
     * Surrounds $str with the given substring.
     *
     * @param string $str
     * @param string $substring <p>The substring to add to both sides.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with the substring both prepended and appended.</p>
     *
     * @template T as string
     * @template TSub as string
     * @phpstan-param T $str
     * @phpstan-param TSub $substring
     * @phpstan-return (T is non-empty-string ? non-empty-string : (TSub is non-empty-string ? non-empty-string : string))
     */
    public static function str_surround(string $str, string $substring): string
    {
        return $substring . $str . $substring;
    }

    /**
     * Returns a trimmed string with the first letter of each word capitalized.
     * Also accepts an array, $ignore, allowing you to list words not to be
     * capitalized.
     *
     * @param string        $str
     * @param string[]|null $ignore                        [optional] <p>An array of words not to capitalize or
     *                                                     null. Default: null</p>
     * @param string        $encoding                      [optional] <p>Default: 'UTF-8'</p>
     * @param bool          $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the
     *                                                     string.</p>
     * @param string|null   $lang                          [optional] <p>Set the language for special cases: az,
     *                                                     el, lt, tr</p>
     * @param bool          $try_to_keep_the_string_length [optional] <p>true === try to keep the string length:
     *                                                     e.g. ẞ -> ß</p>
     * @param bool          $use_trim_first                [optional] <p>true === trim the input string,
     *                                                     first</p>
     * @param string|null   $word_define_chars             [optional] <p>An string of chars that will be used as
     *                                                     whitespace separator === words.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The titleized string.</p>
     */
    public static function str_titleize(
        string $str,
        ?array $ignore = null,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false,
        bool $use_trim_first = true,
        ?string $word_define_chars = null
    ): string {
        if ($str === '') {
            return '';
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($use_trim_first) {
            $str = \trim($str);
        }

        if ($clean_utf8) {
            $str = self::clean($str);
        }

        $use_mb_functions = $lang === null && !$try_to_keep_the_string_length;

        if ($word_define_chars) {
            $word_define_chars = \preg_quote($word_define_chars, '/');
        } else {
            $word_define_chars = '';
        }

        $str = (string) \preg_replace_callback(
            '/([^\\s' . $word_define_chars . ']+)/u',
            static function (array $match) use ($try_to_keep_the_string_length, $lang, $ignore, $use_mb_functions, $encoding): string {
                if ($ignore !== null && \in_array($match[0], $ignore, true)) {
                    return $match[0];
                }

                if ($use_mb_functions) {
                    if ($encoding === self::UTF8) {
                        return \mb_strtoupper(\mb_substr($match[0], 0, 1))
                               . \mb_strtolower(\mb_substr($match[0], 1));
                    }

                    return \mb_strtoupper(\mb_substr($match[0], 0, 1, $encoding), $encoding)
                           . \mb_strtolower(\mb_substr($match[0], 1, null, $encoding), $encoding);
                }

                return self::ucfirst(
                    self::strtolower(
                        $match[0],
                        $encoding,
                        false,
                        $lang,
                        $try_to_keep_the_string_length
                    ),
                    $encoding,
                    false,
                    $lang,
                    $try_to_keep_the_string_length
                );
            },
            $str
        );

        return $str;
    }

    /**
     * Convert a string into a obfuscate string.
     *
     * EXAMPLE: <code>
     *
     * UTF8::str_obfuscate('lars@moelleken.org', 0.5, '*', ['@', '.']); // e.g. "l***@m**lleke*.*r*"
     * </code>
     *
     * @param string   $str
     * @param float    $percent
     * @param string   $obfuscateChar
     * @param string[] $keepChars
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The obfuscate string.</p>
     */
    public static function str_obfuscate(
        string $str,
        float $percent = 0.5,
        string $obfuscateChar = '*',
        array $keepChars = []
    ): string {
        $obfuscateCharHelper = "\u{2603}";
        $str = \str_replace($obfuscateChar, $obfuscateCharHelper, $str);

        $chars = self::chars($str);
        $charsMax = \count($chars);
        $charsMaxChange = \round($charsMax * $percent);
        $charsCounter = 0;
        $charKeyDone = [];

        while ($charsCounter < $charsMaxChange) {
            foreach ($chars as $charKey => $char) {
                if (isset($charKeyDone[$charKey])) {
                    continue;
                }

                if (\random_int(0, 100) > 50) {
                    continue;
                }

                if ($char === $obfuscateChar) {
                    continue;
                }

                ++$charsCounter;
                $charKeyDone[$charKey] = true;

                if ($charsCounter > $charsMaxChange) {
                    break;
                }

                if (\in_array($char, $keepChars, true)) {
                    continue;
                }

                $chars[$charKey] = $obfuscateChar;
            }
        }

        $str = \implode('', $chars);

        return \str_replace($obfuscateCharHelper, $obfuscateChar, $str);
    }

    /**
     * Returns a trimmed string in proper title case.
     *
     * Also accepts an array, $ignore, allowing you to list words not to be
     * capitalized.
     *
     * Adapted from John Gruber's script.
     *
     * @see https://gist.github.com/gruber/9f9e8650d68b13ce4d78
     *
     * @param string   $str
     * @param string[] $ignore   <p>An array of words not to capitalize.</p>
     * @param string   $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The titleized string.</p>
     */
    public static function str_titleize_for_humans(
        string $str,
        array $ignore = [],
        string $encoding = self::UTF8
    ): string {
        if ($str === '') {
            return '';
        }

        $small_words = [
            '(?<!q&)a',
            'an',
            'and',
            'as',
            'at(?!&t)',
            'but',
            'by',
            'en',
            'for',
            'if',
            'in',
            'of',
            'on',
            'or',
            'the',
            'to',
            'v[.]?',
            'via',
            'vs[.]?',
        ];

        if ($ignore !== []) {
            $small_words = \array_merge($small_words, $ignore);
        }

        $small_words_rx = \implode('|', $small_words);
        $apostrophe_rx = '(?x: [\'’] [[:lower:]]* )?';

        $str = \trim($str);

        if (!self::has_lowercase($str)) {
            $str = self::strtolower($str, $encoding);
        }

        // the main substitutions
        $str = (string) \preg_replace_callback(
            '~\\b (_*) (?:                                                                  # 1. Leading underscore and
                        ( (?<=[ ][/\\\\]) [[:alpha:]]+ [-_[:alpha:]/\\\\]+ |                # 2. file path or
                          [-_[:alpha:]]+ [@.:] [-_[:alpha:]@.:/]+ ' . $apostrophe_rx . ' )  #    URL, domain, or email
                        |                                                                   #
                        ( (?i: ' . $small_words_rx . ' ) ' . $apostrophe_rx . ' )           # 3. or small word (case-insensitive)
                        |                                                                   #
                        ( [[:alpha:]] [[:lower:]\'’()\[\]{}]* ' . $apostrophe_rx . ' )      # 4. or word w/o internal caps
                        |                                                                   #
                        ( [[:alpha:]] [[:alpha:]\'’()\[\]{}]* ' . $apostrophe_rx . ' )      # 5. or some other word
                      ) (_*) \\b                                                            # 6. With trailing underscore
                    ~ux',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                // preserve leading underscore
                $str = $matches[1];
                if ($matches[2]) {
                    // preserve URLs, domains, emails and file paths
                    $str .= $matches[2];
                } elseif ($matches[3]) {
                    // lower-case small words
                    $str .= self::strtolower($matches[3], $encoding);
                } elseif ($matches[4]) {
                    // capitalize word w/o internal caps
                    $str .= static::ucfirst($matches[4], $encoding);
                } else {
                    // preserve other kinds of word (iPhone)
                    $str .= $matches[5];
                }
                // preserve trailing underscore
                $str .= $matches[6];

                return $str;
            },
            $str
        );

        // Exceptions for small words: capitalize at start of title...
        $str = (string) \preg_replace_callback(
            '~(  \\A [[:punct:]]*            # start of title...
                      |  [:.;?!][ ]+                # or of subsentence...
                      |  [ ][\'"“‘(\[][ ]* )        # or of inserted subphrase...
                      ( ' . $small_words_rx . ' ) \\b # ...followed by small word
                     ~uxi',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                return $matches[1] . static::ucfirst($matches[2], $encoding);
            },
            $str
        );

        // ...and end of title
        $str = (string) \preg_replace_callback(
            '~\\b ( ' . $small_words_rx . ' ) # small word...
                      (?= [[:punct:]]* \Z          # ...at the end of the title...
                      |   [\'"’”)\]] [ ] )         # ...or of an inserted subphrase?
                     ~uxi',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                return static::ucfirst($matches[1], $encoding);
            },
            $str
        );

        // Exceptions for small words in hyphenated compound words.
        // e.g. "in-flight" -> In-Flight
        $str = (string) \preg_replace_callback(
            '~\\b
                        (?<! -)                   # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (in-flight)
                        ( ' . $small_words_rx . ' )
                        (?= -[[:alpha:]]+)        # lookahead for "-someword"
                       ~uxi',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                return static::ucfirst($matches[1], $encoding);
            },
            $str
        );

        // e.g. "Stand-in" -> "Stand-In" (Stand is already capped at this point)
        $str = (string) \preg_replace_callback(
            '~\\b
                      (?<!…)                    # Negative lookbehind for a hyphen; we do not want to match man-in-the-middle but do want (stand-in)
                      ( [[:alpha:]]+- )         # $1 = first word and hyphen, should already be properly capped
                      ( ' . $small_words_rx . ' ) # ...followed by small word
                      (?!	- )                 # Negative lookahead for another -
                     ~uxi',
            /**
             * @param string[] $matches
             *
             * @psalm-pure
             *
             * @return string
             */
            static function (array $matches) use ($encoding): string {
                return $matches[1] . static::ucfirst($matches[2], $encoding);
            },
            $str
        );

        return $str;
    }

    /**
     * Get a binary representation of a specific string.
     *
     * EXAPLE: <code>UTF8::str_to_binary('😃'); // '11110000100111111001100010000011'</code>
     *
     * @param string $str <p>The input string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>false on error</p>
     */
    public static function str_to_binary(string $str)
    {
        $value = \unpack('H*', $str);
        if ($value === false) {
            return false;
        }

        return \base_convert($value[1], 16, 2);
    }

    /**
     * @param string   $str
     * @param bool     $remove_empty_values <p>Remove empty values.</p>
     * @param int|null $remove_short_values <p>The min. string length or null to disable</p>
     *
     * @psalm-pure
     *
     * @return string[]
     */
    public static function str_to_lines(string $str, bool $remove_empty_values = false, ?int $remove_short_values = null): array
    {
        if ($str === '') {
            return $remove_empty_values ? [] : [''];
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            $return = \mb_split("[\r\n]{1,2}", $str);
        } else {
            $return = \preg_split("/[\r\n]{1,2}/u", $str);
        }

        if ($return === false) {
            return $remove_empty_values ? [] : [''];
        }

        if (
            $remove_short_values === null
            &&
            !$remove_empty_values
        ) {
            return $return;
        }

        return self::reduce_string_array(
            $return,
            $remove_empty_values,
            $remove_short_values
        );
    }

    /**
     * Convert a string into an array of words.
     *
     * EXAMPLE: <code>UTF8::str_to_words('中文空白 oöäü#s', '#') // array('', '中文空白', ' ', 'oöäü#s', '')</code>
     *
     * @param string   $str
     * @param string   $char_list           <p>Additional chars for the definition of "words".</p>
     * @param bool     $remove_empty_values <p>Remove empty values.</p>
     * @param int|null $remove_short_values <p>The min. string length or null to disable</p>
     *
     * @psalm-pure
     *
     * @return list<string>
     *
     * @phpstan-return ($remove_empty_values is true ? list<string> : non-empty-list<string>)
     */
    public static function str_to_words(
        string $str,
        string $char_list = '',
        bool $remove_empty_values = false,
        ?int $remove_short_values = null
    ): array {
        if ($str === '') {
            return $remove_empty_values ? [] : [''];
        }

        $char_list = self::rxClass($char_list, '\pL');

        $return = \preg_split("/({$char_list}+(?:[\p{Pd}’']{$char_list}+)*)/u", $str, -1, \PREG_SPLIT_DELIM_CAPTURE);
        if ($return === false) {
            return $remove_empty_values ? [] : [''];
        }

        if (
            $remove_short_values === null
            &&
            !$remove_empty_values
        ) {
            return $return;
        }

        $tmp_return = self::reduce_string_array(
            $return,
            $remove_empty_values,
            $remove_short_values
        );

        foreach ($tmp_return as &$item) {
            $item = (string) $item;
        }

        return $tmp_return;
    }

    /**
     * Truncates the string to a given length. If $substring is provided, and
     * truncating occurs, the string is further truncated so that the substring
     * may be appended without exceeding the desired length.
     *
     * @param string $str
     * @param int    $length    <p>Desired length of the truncated string.</p>
     * @param string $substring [optional] <p>The substring to append if it can fit. Default: ''</p>
     * @param string $encoding  [optional] <p>Default: 'UTF-8'</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string after truncating.</p>
     */
    public static function str_truncate(
        string $str,
        int $length,
        string $substring = '',
        string $encoding = self::UTF8
    ): string {
        if ($str === '') {
            return '';
        }

        if ($encoding === self::UTF8) {
            if ($length >= (int) \mb_strlen($str)) {
                return $str;
            }

            if ($substring !== '') {
                $length -= (int) \mb_strlen($substring);

                /** @noinspection UnnecessaryCastingInspection */
                return (string) \mb_substr($str, 0, $length) . $substring;
            }

            return (string) \mb_substr($str, 0, $length);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ($length >= (int) self::strlen($str, $encoding)) {
            return $str;
        }

        if ($substring !== '') {
            $length -= (int) self::strlen($substring, $encoding);
        }

        return (
               (string) self::substr(
                   $str,
                   0,
                   $length,
                   $encoding
               )
               ) . $substring;
    }

    /**
     * Truncates the string to a given length, while ensuring that it does not
     * split words. If $substring is provided, and truncating occurs, the
     * string is further truncated so that the substring may be appended without
     * exceeding the desired length.
     *
     * @param string $str
     * @param int    $length                                 <p>Desired length of the truncated string.</p>
     * @param string $substring                              [optional] <p>The substring to append if it can fit.
     *                                                       Default:
     *                                                       ''</p>
     * @param string $encoding                               [optional] <p>Default: 'UTF-8'</p>
     * @param bool   $ignore_do_not_split_words_for_one_word [optional] <p>Default: false</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string after truncating.</p>
     */
    public static function str_truncate_safe(
        string $str,
        int $length,
        string $substring = '',
        string $encoding = self::UTF8,
        bool $ignore_do_not_split_words_for_one_word = false
    ): string {
        if ($str === '' || $length <= 0) {
            return $substring;
        }

        if ($encoding === self::UTF8) {
            if ($length >= (int) \mb_strlen($str)) {
                return $str;
            }

            // need to further trim the string so we can append the substring
            $length -= (int) \mb_strlen($substring);
            if ($length <= 0) {
                return $substring;
            }

            /** @var false|string $truncated - needed for PhpStan (stubs error) */
            $truncated = \mb_substr($str, 0, $length);
            if ($truncated === false) {
                return '';
            }

            // if the last word was truncated
            $space_position = \mb_strpos($str, ' ', $length - 1);
            if ($space_position !== $length) {
                // find pos of the last occurrence of a space, get up to that
                $last_position = \mb_strrpos($truncated, ' ', 0);

                if (
                    $last_position !== false
                    ||
                    (
                        $space_position !== false
                        &&
                        !$ignore_do_not_split_words_for_one_word
                    )
                ) {
                    $truncated = (string) \mb_substr($truncated, 0, (int) $last_position);
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            if ($length >= (int) self::strlen($str, $encoding)) {
                return $str;
            }

            // need to further trim the string so we can append the substring
            $length -= (int) self::strlen($substring, $encoding);
            if ($length <= 0) {
                return $substring;
            }

            $truncated = self::substr($str, 0, $length, $encoding);

            if ($truncated === false) {
                return '';
            }

            // if the last word was truncated
            $space_position = self::strpos($str, ' ', $length - 1, $encoding);
            if ($space_position !== $length) {
                // find pos of the last occurrence of a space, get up to that
                $last_position = self::strrpos($truncated, ' ', 0, $encoding);

                if (
                    $last_position !== false
                    ||
                    (
                        $space_position !== false
                        &&
                        !$ignore_do_not_split_words_for_one_word
                    )
                ) {
                    $truncated = (string) self::substr($truncated, 0, (int) $last_position, $encoding);
                }
            }
        }

        return $truncated . $substring;
    }

    /**
     * Returns a lowercase and trimmed string separated by underscores.
     * Underscores are inserted before uppercase characters (with the exception
     * of the first character of the string), and in place of spaces as well as
     * dashes.
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The underscored string.</p>
     */
    public static function str_underscored(string $str): string
    {
        return self::str_delimit($str, '_');
    }

    /**
     * Returns an UpperCamelCase version of the supplied string. It trims
     * surrounding spaces, capitalizes letters following digits, spaces, dashes
     * and underscores, and removes spaces, dashes, underscores.
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string      $encoding                      [optional] <p>Default: 'UTF-8'</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string in UpperCamelCase.</p>
     */
    public static function str_upper_camelize(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        return self::ucfirst(self::str_camelize($str, $encoding), $encoding, $clean_utf8, $lang, $try_to_keep_the_string_length);
    }

    /**
     * Get the number of words in a specific string.
     *
     * EXAMPLES: <code>
     * // format: 0 -> return only word count (int)
     * //
     * UTF8::str_word_count('中文空白 öäü abc#c'); // 4
     * UTF8::str_word_count('中文空白 öäü abc#c', 0, '#'); // 3
     *
     * // format: 1 -> return words (array)
     * //
     * UTF8::str_word_count('中文空白 öäü abc#c', 1); // array('中文空白', 'öäü', 'abc', 'c')
     * UTF8::str_word_count('中文空白 öäü abc#c', 1, '#'); // array('中文空白', 'öäü', 'abc#c')
     *
     * // format: 2 -> return words with offset (array)
     * //
     * UTF8::str_word_count('中文空白 öäü ab#c', 2); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc', 13 => 'c')
     * UTF8::str_word_count('中文空白 öäü ab#c', 2, '#'); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc#c')
     * </code>
     *
     * @param string $str       <p>The input string.</p>
     * @param int    $format    [optional] <p>
     *                          <strong>0</strong> => return a number of words (default)<br>
     *                          <strong>1</strong> => return an array of words<br>
     *                          <strong>2</strong> => return an array of words with word-offset as key
     *                          </p>
     * @param string $char_list [optional] <p>Additional chars that contains to words and do not start a new word.</p>
     *
     * @psalm-pure
     *
     * @return int|string[]
     *                      <p>The number of words in the string.</p>
     *
     * @phpstan-param 0|1|2 $format
     * @phpstan-return ($format is 2 ? array<int, string> : ($format is 1 ? list<string> : 0|positive-int))
     */
    public static function str_word_count(string $str, int $format = 0, string $char_list = '')
    {
        $str_parts = self::str_to_words($str, $char_list);

        $len = \count($str_parts);

        if ($format === 1) {
            $number_of_words = [];
            for ($i = 1; $i < $len; $i += 2) {
                $number_of_words[] = $str_parts[$i];
            }

            return $number_of_words;
        }

        if ($format === 2) {
            $number_of_words = [];
            $offset = (int) self::strlen($str_parts[0]);
            for ($i = 1; $i < $len; $i += 2) {
                $number_of_words[$offset] = $str_parts[$i];
                $offset += (int) self::strlen($str_parts[$i]) + (int) self::strlen($str_parts[$i + 1]);
            }

            return $number_of_words;
        }

        $number_of_words = (int) (($len - 1) / 2);

        /* @phpstan-ignore-next-line | it should be 0|positive-int, maybe nested "phpstan-return" is not working? */
        return $number_of_words;
    }

    /**
     * Case-insensitive string comparison.
     *
     * INFO: Case-insensitive version of UTF8::strcmp()
     *
     * EXAMPLE: <code>UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn", "Iñtërnâtiôn\nàlizætiøn"); // 0</code>
     *
     * @param string $str1     <p>The first string.</p>
     * @param string $str2     <p>The second string.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
     *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strcasecmp(
        string $str1,
        string $str2,
        string $encoding = self::UTF8
    ): int {
        return self::strcmp(
            self::strtocasefold(
                $str1,
                true,
                false,
                $encoding,
                null,
                false
            ),
            self::strtocasefold(
                $str2,
                true,
                false,
                $encoding,
                null,
                false
            )
        );
    }

    /**
     * Case-sensitive string comparison.
     *
     * EXAMPLE: <code>UTF8::strcmp("iñtërnâtiôn\nàlizætiøn", "iñtërnâtiôn\nàlizætiøn"); // 0</code>
     *
     * @param string $str1 <p>The first string.</p>
     * @param string $str2 <p>The second string.</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if str1 is less than str2<br>
     *             <strong>&gt; 0</strong> if str1 is greater than str2<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strcmp(string $str1, string $str2): int
    {
        if ($str1 === $str2) {
            return 0;
        }

        return \strcmp(
            \Normalizer::normalize($str1, \Normalizer::NFD),
            \Normalizer::normalize($str2, \Normalizer::NFD)
        );
    }

    /**
     * Find length of initial segment not matching mask.
     *
     * @param string   $str
     * @param string   $char_list
     * @param int      $offset
     * @param int|null $length
     * @param string   $encoding  [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *
     * @phpstan-return 0|positive-int
     */
    public static function strcspn(
        string $str,
        string $char_list,
        int $offset = 0,
        ?int $length = null,
        string $encoding = self::UTF8
    ): int {
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($char_list === '') {
            return (int) self::strlen($str, $encoding);
        }

        if ($offset || $length !== null) {
            if ($encoding === self::UTF8) {
                if ($length === null) {
                    $str_tmp = \mb_substr($str, $offset);
                } else {
                    $str_tmp = \mb_substr($str, $offset, $length);
                }
            } else {
                $str_tmp = self::substr($str, $offset, $length, $encoding);
            }

            if ($str_tmp === false) {
                return 0;
            }

            $str = $str_tmp;
        }

        if ($str === '') {
            return 0;
        }

        $matches = [];
        if (\preg_match('/^(.*?)' . self::rxClass($char_list) . '/us', $str, $matches)) {
            $return = self::strlen($matches[1], $encoding);
            if ($return === false) {
                return 0;
            }

            return $return;
        }

        return (int) self::strlen($str, $encoding);
    }

    /**
     * Create a UTF-8 string from code points.
     *
     * INFO: opposite to UTF8::codepoints()
     *
     * EXAMPLE: <code>UTF8::string(array(246, 228, 252)); // 'öäü'</code>
     *
     * @param int|int[]|string|string[] $intOrHex <p>Integer or Hexadecimal codepoints.</p>
     *
     * @phpstan-param int[]|numeric-string[]|int|numeric-string $intOrHex
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A UTF-8 encoded string.</p>
     */
    public static function string($intOrHex): string
    {
        if ($intOrHex === []) {
            return '';
        }

        if (!\is_array($intOrHex)) {
            $intOrHex = [$intOrHex];
        }

        $str = '';
        foreach ($intOrHex as $strPart) {
            $str .= '&#' . (int) $strPart . ';';
        }

        // We cannot use html_entity_decode() here, as it will not return
        // characters for many values < 160.
        return mb_convert_encoding($str, self::UTF8, self::HTML_ENTITIES);
    }

    /**
     * Checks if a string starts with a BOM (Byte Order Mark) character.
     *
     * Example: UTF8::hasBom("\xEF\xBB\xBF foobar"); // true
     *
     * @param string $str The input string.
     *
     * @psalm-pure
     *
     * @return bool True if the string has a BOM at the start, false otherwise.
     */
    public static function hasBom(string $str): bool
    {
        foreach (self::$BOM as $bomString => $bomByteLength) {
            if (\strncmp($str, $bomString, $bomByteLength) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Strip HTML and PHP tags from a string + clean invalid UTF-8.
     *
     * EXAMPLE: <code>UTF8::strip_tags("<span>κόσμε\xa0\xa1</span>"); // 'κόσμε'</code>
     *
     * @see http://php.net/manual/en/function.strip-tags.php
     *
     * @param string      $str            <p>
     *                                    The input string.
     *                                    </p>
     * @param string|null $allowable_tags [optional] <p>
     *                                    You can use the optional second parameter to specify tags which should
     *                                    not be stripped.
     *                                    </p>
     *                                    <p>
     *                                    HTML comments and PHP tags are also stripped. This is hardcoded and
     *                                    can not be changed with allowable_tags.
     *                                    </p>
     * @param bool        $clean_utf8     [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The stripped string.</p>
     */
    public static function strip_tags(
        string $str,
        ?string $allowable_tags = null,
        bool $clean_utf8 = false
    ): string {
        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            $str = self::clean($str);
        }

        if ($allowable_tags === null) {
            return \strip_tags($str);
        }

        return \strip_tags($str, $allowable_tags);
    }

    /**
     * Strip all whitespace characters. This includes tabs and newline
     * characters, as well as multibyte whitespace such as the thin space
     * and ideographic space.
     *
     * EXAMPLE: <code>UTF8::strip_whitespace('   Ο     συγγραφέας  '); // 'Οσυγγραφέας'</code>
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function strip_whitespace(string $str): string
    {
        if ($str === '') {
            return '';
        }

        return (string) \preg_replace('/[[:space:]]+/u', '', $str);
    }

    /**
     * Find the position of the first occurrence of a substring in a string, case-insensitive.
     *
     * INFO: use UTF8::stripos_in_byte() for the byte-length
     *
     * EXAMPLE: <code>UTF8::stripos('aσσb', 'ΣΣ'); // 1</code> (σσ == ΣΣ)
     *
     * @see http://php.net/manual/en/function.mb-stripos.php
     *
     * @param string $haystack   <p>The string from which to get the position of the first occurrence of needle.</p>
     * @param string $needle     <p>The string to find in haystack.</p>
     * @param int    $offset     [optional] <p>The position in haystack to start searching.</p>
     * @param string $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   Return the <strong>(int)</strong> numeric position of the first occurrence of needle in the
     *                   haystack string,<br> or <strong>false</strong> if needle is not found
     *
     * @phpstan-return false|0|positive-int
     */
    public static function stripos(
        string $haystack,
        string $needle,
        int $offset = 0,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return 0;
            }

            return false;
        }

        if ($needle === '' && \PHP_VERSION_ID < 80000) {
            return false;
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $haystack = self::clean($haystack);
            $needle = self::clean($needle);
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_stripos($haystack, $needle, $offset);
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return \mb_stripos($haystack, $needle, $offset, $encoding);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if (
            $encoding === self::UTF8 // INFO: "grapheme_stripos()" can't handle other encodings
            &&
            $offset >= 0 // grapheme_stripos() can't handle negative offset
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_stripos($haystack, $needle, $offset);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($haystack . $needle)) {
            return \stripos($haystack, $needle, $offset);
        }

        //
        // fallback via vanilla php
        //

        $haystack = self::strtocasefold($haystack, true, false, $encoding, null, false);
        $needle = self::strtocasefold($needle, true, false, $encoding, null, false);

        return self::strpos($haystack, $needle, $offset, $encoding);
    }

    /**
     * Returns all of haystack starting from and including the first occurrence of needle to the end.
     *
     * EXAMPLE: <code>
     * $str = 'iñtërnâtiônàlizætiøn';
     * $search = 'NÂT';
     *
     * UTF8::stristr($str, $search)); // 'nâtiônàlizætiøn'
     * UTF8::stristr($str, $search, true)); // 'iñtër'
     * </code>
     *
     * @param string $haystack      <p>The input string. Must be valid UTF-8.</p>
     * @param string $needle        <p>The string to look for. Must be valid UTF-8.</p>
     * @param bool   $before_needle [optional] <p>
     *                              If <b>TRUE</b>, it returns the part of the
     *                              haystack before the first occurrence of the needle (excluding the needle).
     *                              </p>
     * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8    [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>A sub-string,<br>or <strong>false</strong> if needle is not found.</p>
     */
    public static function stristr(
        string $haystack,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return '';
            }

            return false;
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($needle === '') {
            if (\PHP_VERSION_ID >= 80000) {
                return $haystack;
            }

            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_stristr($haystack, $needle, $before_needle);
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return \mb_stristr($haystack, $needle, $before_needle, $encoding);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::stristr() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        if (
            $encoding === self::UTF8 // INFO: "grapheme_stristr()" can't handle other encodings
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_stristr($haystack, $needle, $before_needle);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        if (ASCII::isAscii($needle . $haystack)) {
            return \stristr($haystack, $needle, $before_needle);
        }

        \preg_match('/^(.*?)' . \preg_quote($needle, '/') . '/usi', $haystack, $match);

        if (!isset($match[1])) {
            return false;
        }

        if ($before_needle) {
            return $match[1];
        }

        return self::substr($haystack, (int) self::strlen($match[1], $encoding), null, $encoding);
    }

    /**
     * Get the string length, not the byte-length!
     *
     * INFO: use UTF8::strwidth() for the char-length
     *
     * EXAMPLE: <code>UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn")); // 20</code>
     *
     * @see http://php.net/manual/en/function.mb-strlen.php
     *
     * @param string $str        <p>The string being checked for length.</p>
     * @param string $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>
     *                   The number <strong>(int)</strong> of characters in the string $str having character encoding
     *                   $encoding.
     *                   (One multi-byte character counted as +1).
     *                   <br>
     *                   Can return <strong>false</strong>, if e.g. mbstring is not installed and we process invalid
     *                   chars.
     *                   </p>
     *
     * @phpstan-return false|0|positive-int
     */
    public static function strlen(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($str === '') {
            return 0;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($clean_utf8) {
            // "mb_strlen" and "\iconv_strlen" returns wrong length,
            // if invalid characters are found in $str
            $str = self::clean($str);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection - ignore warnings, it's working anyway */
                return @\mb_strlen($str);
            }

            /** @noinspection PhpUsageOfSilenceOperatorInspection - ignore warnings, it's working anyway */
            return @\mb_strlen($str, $encoding);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            return \strlen($str);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
            &&
            self::$SUPPORT[self::FEATURE_ICONV] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strlen() without mbstring / iconv cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via iconv
        //

        if (self::$SUPPORT[self::FEATURE_ICONV] === true) {
            $return_tmp = \iconv_strlen($str, $encoding);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback via intl
        //

        if (
            $encoding === self::UTF8 // INFO: "grapheme_strlen()" can't handle other encodings
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_strlen($str);
            /* @phpstan-ignore-next-line | "grapheme_strlen" will maybe return "null" for empty-strings and "false" on error */
            if ($return_tmp !== false && $return_tmp !== null) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($str)) {
            return \strlen($str);
        }

        //
        // fallback via vanilla php
        //

        \preg_match_all('/./us', $str, $parts);

        $return_tmp = \count($parts[0]);
        if ($return_tmp === 0) {
            return false;
        }

        return $return_tmp;
    }

    /**
     * Get string length in byte.
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return int
     *
     * @phpstan-return 0|positive-int
     */
    public static function strlen_in_byte(string $str): int
    {
        if ($str === '') {
            return 0;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_strlen($str, self::CP850); // 8-BIT
        }

        return \strlen($str);
    }

    /**
     * Case-insensitive string comparisons using a "natural order" algorithm.
     *
     * INFO: natural order version of UTF8::strcasecmp()
     *
     * EXAMPLES: <code>
     * UTF8::strnatcasecmp('2', '10Hello WORLD 中文空白!'); // -1
     * UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1
     *
     * UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
     * UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
     * </code>
     *
     * @param string $str1     <p>The first string.</p>
     * @param string $str2     <p>The second string.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if str1 is less than str2<br>
     *             <strong>&gt; 0</strong> if str1 is greater than str2<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strnatcasecmp(string $str1, string $str2, string $encoding = self::UTF8): int
    {
        return self::strnatcmp(
            self::strtocasefold($str1, true, false, $encoding, null, false),
            self::strtocasefold($str2, true, false, $encoding, null, false)
        );
    }

    /**
     * String comparisons using a "natural order" algorithm
     *
     * INFO: natural order version of UTF8::strcmp()
     *
     * EXAMPLES: <code>
     * UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // -1
     * UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1
     *
     * UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
     * UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
     * </code>
     *
     * @see http://php.net/manual/en/function.strnatcmp.php
     *
     * @param string $str1 <p>The first string.</p>
     * @param string $str2 <p>The second string.</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
     *             <strong>&gt; 0</strong> if str1 is greater than str2;<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strnatcmp(string $str1, string $str2): int
    {
        if ($str1 === $str2) {
            return 0;
        }

        return \strnatcmp(
            (string) self::strtonatfold($str1),
            (string) self::strtonatfold($str2)
        );
    }

    /**
     * Case-insensitive string comparison of the first n characters.
     *
     * EXAMPLE: <code>
     * UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn321", "iñtërnâtiôn\nàlizætiøn123", 5); // 0
     * </code>
     *
     * @see http://php.net/manual/en/function.strncasecmp.php
     *
     * @param string $str1     <p>The first string.</p>
     * @param string $str2     <p>The second string.</p>
     * @param int    $len      <p>The length of strings to be used in the comparison.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
     *             <strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strncasecmp(
        string $str1,
        string $str2,
        int $len,
        string $encoding = self::UTF8
    ): int {
        return self::strncmp(
            self::strtocasefold($str1, true, false, $encoding, null, false),
            self::strtocasefold($str2, true, false, $encoding, null, false),
            $len
        );
    }

    /**
     * String comparison of the first n characters.
     *
     * EXAMPLE: <code>
     * UTF8::strncmp("Iñtërnâtiôn\nàlizætiøn321", "Iñtërnâtiôn\nàlizætiøn123", 5); // 0
     * </code>
     *
     * @see http://php.net/manual/en/function.strncmp.php
     *
     * @param string $str1     <p>The first string.</p>
     * @param string $str2     <p>The second string.</p>
     * @param int    $len      <p>Number of characters to use in the comparison.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
     *             <strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
     *             <strong>0</strong> if they are equal
     */
    public static function strncmp(
        string $str1,
        string $str2,
        int $len,
        string $encoding = self::UTF8
    ): int {
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($encoding === self::UTF8) {
            $str1 = (string) \mb_substr($str1, 0, $len);
            $str2 = (string) \mb_substr($str2, 0, $len);
        } else {
            $str1 = (string) self::substr($str1, 0, $len, $encoding);
            $str2 = (string) self::substr($str2, 0, $len, $encoding);
        }

        return self::strcmp($str1, $str2);
    }

    /**
     * Search a string for any of a set of characters.
     *
     * EXAMPLE: <code>UTF8::strpbrk('-中文空白-', '白'); // '白-'</code>
     *
     * @see http://php.net/manual/en/function.strpbrk.php
     *
     * @param string $haystack  <p>The string where char_list is looked for.</p>
     * @param string $char_list <p>This parameter is case-sensitive.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The string starting from the character found, or false if it is not found.</p>
     */
    public static function strpbrk(string $haystack, string $char_list)
    {
        if ($haystack === '' || $char_list === '') {
            return false;
        }

        if (\preg_match('/' . self::rxClass($char_list) . '/us', $haystack, $m)) {
            return \substr($haystack, (int) \strpos($haystack, $m[0]));
        }

        return false;
    }

    /**
     * Find the position of the first occurrence of a substring in a string.
     *
     * INFO: use UTF8::strpos_in_byte() for the byte-length
     *
     * EXAMPLE: <code>UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8</code>
     *
     * @see http://php.net/manual/en/function.mb-strpos.php
     *
     * @param string     $haystack   <p>The string from which to get the position of the first occurrence of needle.</p>
     * @param int|string $needle     <p>The string to find in haystack.<br>Or a code point as int.</p>
     * @param int        $offset     [optional] <p>The search offset. If it is not specified, 0 is used.</p>
     * @param string     $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool       $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   The <strong>(int)</strong> numeric position of the first occurrence of needle in the haystack
     *                   string.<br> If needle is not found it returns false.
     *
     * @phpstan-return false|0|positive-int
     */
    public static function strpos(
        string $haystack,
        $needle,
        int $offset = 0,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000) {
                if ($needle === '') {
                    return 0;
                }
            } else {
                return false;
            }
        }

        // iconv and mbstring do not support integer $needle
        if ((int) $needle === $needle) {
            $needle = (string) self::chr($needle);
        }
        $needle = (string) $needle;

        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return 0;
            }

            return false;
        }

        if ($needle === '' && \PHP_VERSION_ID < 80000) {
            return false;
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                /** @noinspection PhpUsageOfSilenceOperatorInspection - Offset not contained in string */
                return @\mb_strpos($haystack, $needle, $offset);
            }

            /** @noinspection PhpUsageOfSilenceOperatorInspection - Offset not contained in string */
            return @\mb_strpos($haystack, $needle, $offset, $encoding);
        }

        //
        // fallback for binary || ascii only
        //
        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            return \strpos($haystack, $needle, $offset);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_ICONV] === false
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strpos() without mbstring / iconv cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via intl
        //

        if (
            $encoding === self::UTF8 // INFO: "grapheme_strpos()" can't handle other encodings
            &&
            $offset >= 0 // grapheme_strpos() can't handle negative offset
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_strpos($haystack, $needle, $offset);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback via iconv
        //

        if (
            $offset >= 0 // iconv_strpos() can't handle negative offset
            &&
            self::$SUPPORT[self::FEATURE_ICONV] === true
        ) {
            // ignore invalid negative offset to keep compatibility
            // with php < 5.5.35, < 5.6.21, < 7.0.6
            $return_tmp = \iconv_strpos($haystack, $needle, $offset > 0 ? $offset : 0, $encoding);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($haystack . $needle)) {
            /** @noinspection PhpUsageOfSilenceOperatorInspection - Offset not contained in string */
            return @\strpos($haystack, $needle, $offset);
        }

        //
        // fallback via vanilla php
        //

        $haystack_tmp = self::substr($haystack, $offset, null, $encoding);
        if ($haystack_tmp === false) {
            $haystack_tmp = '';
        }
        $haystack = (string) $haystack_tmp;

        if ($offset < 0) {
            $offset = 0;
        }

        $pos = \strpos($haystack, $needle);
        if ($pos === false) {
            return false;
        }

        if ($pos) {
            return $offset + (int) self::strlen(\substr($haystack, 0, $pos), $encoding);
        }

        return $offset + 0;
    }

    /**
     * Find the position of the first occurrence of a substring in a string.
     *
     * @param string $haystack <p>
     *                         The string being checked.
     *                         </p>
     * @param string $needle   <p>
     *                         The position counted from the beginning of haystack.
     *                         </p>
     * @param int    $offset   [optional] <p>
     *                         The search offset. If it is not specified, 0 is used.
     *                         </p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The numeric position of the first occurrence of needle in the
     *                   haystack string. If needle is not found, it returns false.</p>
     *
     * @phpstan-return false|0|positive-int
     */
    public static function strpos_in_byte(string $haystack, string $needle, int $offset = 0)
    {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_strpos($haystack, $needle, $offset, self::CP850); // 8-BIT
        }

        return \strpos($haystack, $needle, $offset);
    }

    /**
     * Find the position of the first occurrence of a substring in a string, case-insensitive.
     *
     * @param string $haystack <p>
     *                         The string being checked.
     *                         </p>
     * @param string $needle   <p>
     *                         The position counted from the beginning of haystack.
     *                         </p>
     * @param int    $offset   [optional] <p>
     *                         The search offset. If it is not specified, 0 is used.
     *                         </p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The numeric position of the first occurrence of needle in the
     *                   haystack string. If needle is not found, it returns false.</p>
     *
     * @phpstan-return false|0|positive-int
     */
    public static function stripos_in_byte(string $haystack, string $needle, int $offset = 0)
    {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_stripos($haystack, $needle, $offset, self::CP850); // 8-BIT
        }

        return \stripos($haystack, $needle, $offset);
    }

    /**
     * Find the last occurrence of a character in a string within another.
     *
     * EXAMPLE: <code>UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε'); // 'κόσμε-äöü'</code>
     *
     * @see http://php.net/manual/en/function.mb-strrchr.php
     *
     * @param string $haystack      <p>The string from which to get the last occurrence of needle.</p>
     * @param string $needle        <p>The string to find in haystack</p>
     * @param bool   $before_needle [optional] <p>
     *                              Determines which portion of haystack
     *                              this function returns.
     *                              If set to true, it returns all of haystack
     *                              from the beginning to the last occurrence of needle.
     *                              If set to false, it returns all of haystack
     *                              from the last occurrence of needle to the end,
     *                              </p>
     * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8    [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The portion of haystack or false if needle is not found.</p>
     */
    public static function strrchr(
        string $haystack,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strrchr($haystack, $needle, $before_needle);
            }

            return \mb_strrchr($haystack, $needle, $before_needle, $encoding);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            !$before_needle
            &&
            (
                $encoding === self::CP850
                ||
                $encoding === self::ASCII
            )
        ) {
            return \strrchr($haystack, $needle);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strrchr() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via iconv
        //

        if (self::$SUPPORT[self::FEATURE_ICONV] === true) {
            $needle_tmp = self::substr($needle, 0, 1, $encoding);
            if ($needle_tmp === false) {
                return false;
            }
            $needle = $needle_tmp;

            $pos = \iconv_strrpos($haystack, $needle, $encoding);
            if ($pos === false) {
                return false;
            }

            if ($before_needle) {
                return self::substr($haystack, 0, $pos, $encoding);
            }

            return self::substr($haystack, $pos, null, $encoding);
        }

        //
        // fallback via vanilla php
        //

        $needle_tmp = self::substr($needle, 0, 1, $encoding);
        if ($needle_tmp === false) {
            return false;
        }
        $needle = $needle_tmp;

        $pos = self::strrpos($haystack, $needle, 0, $encoding);
        if ($pos === false) {
            return false;
        }

        if ($before_needle) {
            return self::substr($haystack, 0, $pos, $encoding);
        }

        return self::substr($haystack, $pos, null, $encoding);
    }

    /**
     * Reverses characters order in the string.
     *
     * EXAMPLE: <code>UTF8::strrev('κ-öäü'); // 'üäö-κ'</code>
     *
     * @param string $str      <p>The input string.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The string with characters in the reverse sequence.</p>
     */
    public static function strrev(string $str, string $encoding = self::UTF8): string
    {
        if ($str === '') {
            return '';
        }

        // init
        $reversed = '';

        $str = self::emojiEncode($str, true);

        if ($encoding === self::UTF8) {
            if (self::$SUPPORT[self::FEATURE_INTL] === true) {
                // try "grapheme" first: https://stackoverflow.com/questions/17496493/strrev-dosent-support-utf-8
                $i = (int) \grapheme_strlen($str);
                while ($i--) {
                    $reversed_tmp = \grapheme_substr($str, $i, 1);
                    if ($reversed_tmp !== false) {
                        $reversed .= $reversed_tmp;
                    }
                }
            } else {
                $i = (int) \mb_strlen($str);
                while ($i--) {
                    $reversed_tmp = \mb_substr($str, $i, 1);
                    if ($reversed_tmp !== false) { /* @phpstan-ignore-line | old polyfill will return false, or? */
                        $reversed .= $reversed_tmp;
                    }
                }
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $i = (int) self::strlen($str, $encoding);
            while ($i--) {
                $reversed_tmp = self::substr($str, $i, 1, $encoding);
                if ($reversed_tmp !== false) {
                    $reversed .= $reversed_tmp;
                }
            }
        }

        return self::emojiDecode($reversed, true);
    }

    /**
     * Find the last occurrence of a character in a string within another, case-insensitive.
     *
     * EXAMPLE: <code>UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε'); // 'Aκόσμεκόσμε-äöü'</code>
     *
     * @see http://php.net/manual/en/function.mb-strrichr.php
     *
     * @param string $haystack      <p>The string from which to get the last occurrence of needle.</p>
     * @param string $needle        <p>The string to find in haystack.</p>
     * @param bool   $before_needle [optional] <p>
     *                              Determines which portion of haystack
     *                              this function returns.
     *                              If set to true, it returns all of haystack
     *                              from the beginning to the last occurrence of needle.
     *                              If set to false, it returns all of haystack
     *                              from the last occurrence of needle to the end,
     *                              </p>
     * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8    [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The portion of haystack or<br>false if needle is not found.</p>
     */
    public static function strrichr(
        string $haystack,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strrichr($haystack, $needle, $before_needle);
            }

            return \mb_strrichr($haystack, $needle, $before_needle, $encoding);
        }

        //
        // fallback via vanilla php
        //

        $needle_tmp = self::substr($needle, 0, 1, $encoding);
        if ($needle_tmp === false) {
            return false;
        }
        $needle = $needle_tmp;

        $pos = self::strripos($haystack, $needle, 0, $encoding);
        if ($pos === false) {
            return false;
        }

        if ($before_needle) {
            return self::substr($haystack, 0, $pos, $encoding);
        }

        return self::substr($haystack, $pos, null, $encoding);
    }

    /**
     * Find the position of the last occurrence of a substring in a string, case-insensitive.
     *
     * EXAMPLE: <code>UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13</code>
     *
     * @param string     $haystack   <p>The string to look in.</p>
     * @param int|string $needle     <p>The string to look for.</p>
     * @param int        $offset     [optional] <p>Number of characters to ignore in the beginning or end.</p>
     * @param string     $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool       $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
     *                   string.<br>If needle is not found, it returns false.</p>
     */
    public static function strripos(
        string $haystack,
        $needle,
        int $offset = 0,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000) {
                if ($needle === '') {
                    return 0;
                }
            } else {
                return false;
            }
        }

        // iconv and mbstring do not support integer $needle
        if ((int) $needle === $needle && $needle >= 0) {
            $needle = (string) self::chr($needle);
        }
        $needle = (string) $needle;

        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return 0;
            }

            return false;
        }

        if ($needle === '' && \PHP_VERSION_ID < 80000) {
            return false;
        }

        if ($clean_utf8) {
            // mb_strripos() && iconv_strripos() is not tolerant to invalid characters
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        //
        // fallback via mbstrig
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strripos($haystack, $needle, $offset);
            }

            return \mb_strripos($haystack, $needle, $offset, $encoding);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            return \strripos($haystack, $needle, $offset);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strripos() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via intl
        //

        if (
            $encoding === self::UTF8 // INFO: "grapheme_strripos()" can't handle other encodings
            &&
            $offset >= 0 // grapheme_strripos() can't handle negative offset
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_strripos($haystack, $needle, $offset);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($haystack . $needle)) {
            return \strripos($haystack, $needle, $offset);
        }

        //
        // fallback via vanilla php
        //

        $haystack = self::strtocasefold($haystack, true, false, $encoding);
        $needle = self::strtocasefold($needle, true, false, $encoding);

        return self::strrpos($haystack, $needle, $offset, $encoding, $clean_utf8);
    }

    /**
     * Finds position of last occurrence of a string within another, case-insensitive.
     *
     * @param string $haystack <p>
     *                         The string from which to get the position of the last occurrence
     *                         of needle.
     *                         </p>
     * @param string $needle   <p>
     *                         The string to find in haystack.
     *                         </p>
     * @param int    $offset   [optional] <p>
     *                         The position in haystack
     *                         to start searching.
     *                         </p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>eturn the numeric position of the last occurrence of needle in the
     *                   haystack string, or false if needle is not found.</p>
     */
    public static function strripos_in_byte(string $haystack, string $needle, int $offset = 0)
    {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_strripos($haystack, $needle, $offset, self::CP850); // 8-BIT
        }

        return \strripos($haystack, $needle, $offset);
    }

    /**
     * Find the position of the last occurrence of a substring in a string.
     *
     * EXAMPLE: <code>UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13</code>
     *
     * @see http://php.net/manual/en/function.mb-strrpos.php
     *
     * @param string     $haystack   <p>The string being checked, for the last occurrence of needle</p>
     * @param int|string $needle     <p>The string to find in haystack.<br>Or a code point as int.</p>
     * @param int        $offset     [optional] <p>May be specified to begin searching an arbitrary number of characters
     *                               into the string. Negative values will stop searching at an arbitrary point prior to
     *                               the end of the string.
     *                               </p>
     * @param string     $encoding   [optional] <p>Set the charset.</p>
     * @param bool       $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
     *                   string.<br>If needle is not found, it returns false.</p>
     */
    public static function strrpos(
        string $haystack,
        $needle,
        int $offset = 0,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000) {
                if ($needle === '') {
                    return 0;
                }
            } else {
                return false;
            }
        }

        // iconv and mbstring do not support integer $needle
        if ((int) $needle === $needle && $needle >= 0) {
            $needle = (string) self::chr($needle);
        }
        $needle = (string) $needle;

        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return 0;
            }

            return false;
        }

        if ($needle === '' && \PHP_VERSION_ID < 80000) {
            return false;
        }

        if ($clean_utf8) {
            // mb_strrpos && iconv_strrpos is not tolerant to invalid characters
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strrpos($haystack, $needle, $offset);
            }

            return \mb_strrpos($haystack, $needle, $offset, $encoding);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            return \strrpos($haystack, $needle, $offset);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strrpos() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via intl
        //

        if (
            $offset >= 0 // grapheme_strrpos() can't handle negative offset
            &&
            $encoding === self::UTF8 // INFO: "grapheme_strrpos()" can't handle other encodings
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_strrpos($haystack, $needle, $offset);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($haystack . $needle)) {
            return \strrpos($haystack, $needle, $offset);
        }

        //
        // fallback via vanilla php
        //

        $haystack_tmp = null;
        if ($offset > 0) {
            $haystack_tmp = self::substr($haystack, $offset);
        } elseif ($offset < 0) {
            $haystack_tmp = self::substr($haystack, 0, $offset);
            $offset = 0;
        }

        if ($haystack_tmp !== null) {
            if ($haystack_tmp === false) {
                $haystack_tmp = '';
            }
            $haystack = (string) $haystack_tmp;
        }

        $pos = \strrpos($haystack, $needle);
        if ($pos === false) {
            return false;
        }

        /** @var false|string $str_tmp - needed for PhpStan (stubs error) */
        $str_tmp = \substr($haystack, 0, $pos);
        if ($str_tmp === false) {
            return false;
        }

        return $offset + (int) self::strlen($str_tmp);
    }

    /**
     * Find the position of the last occurrence of a substring in a string.
     *
     * @param string $haystack <p>
     *                         The string being checked, for the last occurrence
     *                         of needle.
     *                         </p>
     * @param string $needle   <p>
     *                         The string to find in haystack.
     *                         </p>
     * @param int    $offset   [optional] <p>May be specified to begin searching an arbitrary number of characters into
     *                         the string. Negative values will stop searching at an arbitrary point
     *                         prior to the end of the string.
     *                         </p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The numeric position of the last occurrence of needle in the
     *                   haystack string. If needle is not found, it returns false.</p>
     */
    public static function strrpos_in_byte(string $haystack, string $needle, int $offset = 0)
    {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_strrpos($haystack, $needle, $offset, self::CP850); // 8-BIT
        }

        return \strrpos($haystack, $needle, $offset);
    }

    /**
     * Finds the length of the initial segment of a string consisting entirely of characters contained within a given
     * mask.
     *
     * EXAMPLE: <code>UTF8::strspn('iñtërnâtiônàlizætiøn', 'itñ'); // '3'</code>
     *
     * @param string   $str      <p>The input string.</p>
     * @param string   $mask     <p>The mask of chars</p>
     * @param int      $offset   [optional]
     * @param int|null $length   [optional]
     * @param string   $encoding [optional] <p>Set the charset.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     */
    public static function strspn(
        string $str,
        string $mask,
        int $offset = 0,
        ?int $length = null,
        string $encoding = self::UTF8
    ) {
        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($offset || $length !== null) {
            if ($encoding === self::UTF8) {
                if ($length === null) {
                    $str = (string) \mb_substr($str, $offset);
                } else {
                    $str = (string) \mb_substr($str, $offset, $length);
                }
            } else {
                $str = (string) self::substr($str, $offset, $length, $encoding);
            }
        }

        if ($str === '' || $mask === '') {
            return 0;
        }

        $matches = [];

        return \preg_match('/^' . self::rxClass($mask) . '+/u', $str, $matches) ? (int) self::strlen($matches[0], $encoding) : 0;
    }

    /**
     * Returns part of haystack string from the first occurrence of needle to the end of haystack.
     *
     * EXAMPLE: <code>
     * $str = 'iñtërnâtiônàlizætiøn';
     * $search = 'nât';
     *
     * UTF8::strstr($str, $search)); // 'nâtiônàlizætiøn'
     * UTF8::strstr($str, $search, true)); // 'iñtër'
     * </code>
     *
     * @param string $haystack      <p>The input string. Must be valid UTF-8.</p>
     * @param string $needle        <p>The string to look for. Must be valid UTF-8.</p>
     * @param bool   $before_needle [optional] <p>
     *                              If <b>TRUE</b>, strstr() returns the part of the
     *                              haystack before the first occurrence of the needle (excluding the needle).
     *                              </p>
     * @param string $encoding      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8    [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>A sub-string,<br>or <strong>false</strong> if needle is not found.</p>
     */
    public static function strstr(
        string $haystack,
        string $needle,
        bool $before_needle = false,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($haystack === '') {
            if (\PHP_VERSION_ID >= 80000 && $needle === '') {
                return '';
            }

            return false;
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($needle === '') {
            if (\PHP_VERSION_ID >= 80000) {
                return $haystack;
            }

            return false;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strstr($haystack, $needle, $before_needle);
            }

            return \mb_strstr($haystack, $needle, $before_needle, $encoding);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            return \strstr($haystack, $needle, $before_needle);
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strstr() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via intl
        //

        if (
            $encoding === self::UTF8 // INFO: "grapheme_strstr()" can't handle other encodings
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_strstr($haystack, $needle, $before_needle);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($haystack . $needle)) {
            return \strstr($haystack, $needle, $before_needle);
        }

        //
        // fallback via vanilla php
        //

        \preg_match('/^(.*?)' . \preg_quote($needle, '/') . '/us', $haystack, $match);

        if (!isset($match[1])) {
            return false;
        }

        if ($before_needle) {
            return $match[1];
        }

        return self::substr($haystack, (int) self::strlen($match[1]));
    }

    /**
     * Finds first occurrence of a string within another.
     *
     * @param string $haystack      <p>
     *                              The string from which to get the first occurrence
     *                              of needle.
     *                              </p>
     * @param string $needle        <p>
     *                              The string to find in haystack.
     *                              </p>
     * @param bool   $before_needle [optional] <p>
     *                              Determines which portion of haystack
     *                              this function returns.
     *                              If set to true, it returns all of haystack
     *                              from the beginning to the first occurrence of needle.
     *                              If set to false, it returns all of haystack
     *                              from the first occurrence of needle to the end,
     *                              </p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The portion of haystack,
     *                      or false if needle is not found.</p>
     */
    public static function strstr_in_byte(
        string $haystack,
        string $needle,
        bool $before_needle = false
    ) {
        if ($haystack === '' || $needle === '') {
            return false;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_strstr($haystack, $needle, $before_needle, self::CP850); // 8-BIT
        }

        return \strstr($haystack, $needle, $before_needle);
    }

    /**
     * Unicode transformation for case-less matching.
     *
     * EXAMPLE: <code>UTF8::strtocasefold('ǰ◌̱'); // 'ǰ◌̱'</code>
     *
     * @see http://unicode.org/reports/tr21/tr21-5.html
     *
     * @param string      $str        <p>The input string.</p>
     * @param bool        $full       [optional] <p>
     *                                <b>true</b>, replace full case folding chars (default)<br>
     *                                <b>false</b>, use only limited static array [UTF8::$COMMON_CASE_FOLD]
     *                                </p>
     * @param bool        $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string      $encoding   [optional] <p>Set the charset.</p>
     * @param string|null $lang       [optional] <p>Set the language for special cases: az, el, lt, tr</p>
     * @param bool        $lower      [optional] <p>Use lowercase string, otherwise use uppercase string. PS: uppercase
     *                                is for some languages better ...</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function strtocasefold(
        string $str,
        bool $full = true,
        bool $clean_utf8 = false,
        string $encoding = self::UTF8,
        ?string $lang = null,
        bool $lower = true
    ): string {
        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        $str = self::fixStrCaseHelper($str, $lower, $full);

        if ($lang === null && $encoding === self::UTF8) {
            if ($lower) {
                return \mb_strtolower($str);
            }

            return \mb_strtoupper($str);
        }

        if ($lower) {
            return self::strtolower($str, $encoding, false, $lang);
        }

        return self::strtoupper($str, $encoding, false, $lang);
    }

    /**
     * Make a string lowercase.
     *
     * EXAMPLE: <code>UTF8::strtolower('DÉJÀ Σσς Iıİi'); // 'déjà σσς iıii'</code>
     *
     * @see http://php.net/manual/en/function.mb-strtolower.php
     *
     * @param string      $str                           <p>The string being lowercased.</p>
     * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>String with all alphabetic characters converted to lowercase.</p>
     */
    public static function strtolower(
        $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        // init
        $str = (string) $str;

        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        // hack for old php version or for the polyfill ...
        if ($try_to_keep_the_string_length) {
            $str = self::fixStrCaseHelper($str, true);
        }

        if ($lang === null && $encoding === self::UTF8) {
            return \mb_strtolower($str);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ($lang !== null) {
            if (self::$SUPPORT[self::FEATURE_INTL] === true) {
                if (self::$INTL_TRANSLITERATOR_LIST === null) {
                    self::$INTL_TRANSLITERATOR_LIST = self::getData('transliterator_list');
                }

                $language_code = $lang . '-Lower';
                if (!\in_array($language_code, self::$INTL_TRANSLITERATOR_LIST, true)) {
                    /**
                     * @psalm-suppress ImpureFunctionCall - this is only a warning
                     */
                    \trigger_error('UTF8::strtolower() cannot handle special language: ' . $lang . ' | supported: ' . \print_r(self::$INTL_TRANSLITERATOR_LIST, true), \E_USER_WARNING);

                    $language_code = 'Any-Lower';
                }

                return (string) \transliterator_transliterate($language_code, $str);
            }

            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strtolower() without intl cannot handle the "lang" parameter: ' . $lang, \E_USER_WARNING);
        }

        // always fallback via symfony polyfill
        return \mb_strtolower($str, $encoding);
    }

    /**
     * Make a string uppercase.
     *
     * EXAMPLE: <code>UTF8::strtoupper('Déjà Σσς Iıİi'); // 'DÉJÀ ΣΣΣ IIİI'</code>
     *
     * @see http://php.net/manual/en/function.mb-strtoupper.php
     *
     * @param string      $str                           <p>The string being uppercased.</p>
     * @param string      $encoding                      [optional] <p>Set the charset.</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>String with all alphabetic characters converted to uppercase.</p>
     */
    public static function strtoupper(
        $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        // init
        $str = (string) $str;

        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        // hack for old php version or for the polyfill ...
        if ($try_to_keep_the_string_length) {
            $str = self::fixStrCaseHelper($str);
        }

        if ($lang === null && $encoding === self::UTF8) {
            return \mb_strtoupper($str);
        }

        $encoding = self::normalize_encoding($encoding, self::UTF8);

        if ($lang !== null) {
            if (self::$SUPPORT[self::FEATURE_INTL] === true) {
                if (self::$INTL_TRANSLITERATOR_LIST === null) {
                    self::$INTL_TRANSLITERATOR_LIST = self::getData('transliterator_list');
                }

                $language_code = $lang . '-Upper';
                if (!\in_array($language_code, self::$INTL_TRANSLITERATOR_LIST, true)) {
                    /**
                     * @psalm-suppress ImpureFunctionCall - this is only a warning
                     */
                    \trigger_error('UTF8::strtoupper() without intl for special language: ' . $lang, \E_USER_WARNING);

                    $language_code = 'Any-Upper';
                }

                return (string) \transliterator_transliterate($language_code, $str);
            }

            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::strtolower() without intl cannot handle the "lang"-parameter: ' . $lang, \E_USER_WARNING);
        }

        // always fallback via symfony polyfill
        return \mb_strtoupper($str, $encoding);
    }

    /**
     * Translate characters or replace sub-strings.
     *
     * EXAMPLE:
     * <code>
     * $array = [
     *     'Hello'   => '○●◎',
     *     '中文空白' => 'earth',
     * ];
     * UTF8::strtr('Hello 中文空白', $array); // '○●◎ earth'
     * </code>
     *
     * @see http://php.net/manual/en/function.strtr.php
     *
     * @param string          $str  <p>The string being translated.</p>
     * @param string|string[] $from <p>The string replacing from.</p>
     * @param string|string[] $to   [optional] <p>The string being translated to to.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>This function returns a copy of str, translating all occurrences of each character in "from"
     *                to the corresponding character in "to".</p>
     */
    public static function strtr(string $str, $from, $to = ''): string
    {
        if ($str === '') {
            return '';
        }

        if ($from === $to) {
            return $str;
        }

        if ($to !== '') {
            if (!\is_array($from)) {
                $from = self::strSplit($from);
            }

            if (!\is_array($to)) {
                $to = self::strSplit($to);
            }

            $count_from = \count($from);
            $count_to = \count($to);

            if ($count_from !== $count_to) {
                if ($count_from > $count_to) {
                    $from = \array_slice($from, 0, $count_to);
                } elseif ($count_from < $count_to) {
                    $to = \array_slice($to, 0, $count_from);
                }
            }

            try {
                $from = \array_combine($from, $to);
            } catch (\Error $e) {
                // PHP >= 8.0 : array_combine() will now throw a ValueError if the number of elements for each array is not equal; previously this function returned false instead.
                $from = false;
            }
            if ($from === false) {
                throw new \InvalidArgumentException('The number of elements for each array isn\'t equal or the arrays are empty: (from: ' . \print_r($from, true) . ' | to: ' . \print_r($to, true) . ')');
            }
        }

        if (\is_string($from)) {
            return \str_replace($from, $to, $str);
        }

        return \strtr($str, $from);
    }

    /**
     * Return the width of a string.
     *
     * INFO: use UTF8::strlen() for the byte-length
     *
     * EXAMPLE: <code>UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn")); // 21</code>
     *
     * @param string $str        <p>The input string.</p>
     * @param string $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return int
     *
     * @phpstan-return 0|positive-int
     */
    public static function strwidth(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ): int {
        if ($str === '') {
            return 0;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($clean_utf8) {
            // iconv and mbstring are not tolerant to invalid encoding
            // further, their behaviour is inconsistent with that of PHP's substr
            $str = self::clean($str);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_strwidth($str);
            }

            return \mb_strwidth($str, $encoding);
        }

        //
        // fallback via vanilla php
        //

        if ($encoding !== self::UTF8) {
            $str = self::encode(self::UTF8, $str, false, $encoding);
        }

        $wide = 0;
        $str = (string) \preg_replace('/[\x{1100}-\x{115F}\x{2329}\x{232A}\x{2E80}-\x{303E}\x{3040}-\x{A4CF}\x{AC00}-\x{D7A3}\x{F900}-\x{FAFF}\x{FE10}-\x{FE19}\x{FE30}-\x{FE6F}\x{FF00}-\x{FF60}\x{FFE0}-\x{FFE6}\x{20000}-\x{2FFFD}\x{30000}-\x{3FFFD}]/u', '', $str, -1, $wide);

        /* @phpstan-ignore-next-line | should return 0|positive-int */
        return ($wide << 1) + (int) self::strlen($str);
    }

    /**
     * Get part of a string.
     *
     * EXAMPLE: <code>UTF8::substr('中文空白', 1, 2); // '文空'</code>
     *
     * @see http://php.net/manual/en/function.mb-substr.php
     *
     * @param string   $str        <p>The string being checked.</p>
     * @param int      $offset     <p>The first position used in str.</p>
     * @param int|null $length     [optional] <p>The maximum length of the returned string.</p>
     * @param string   $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool     $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      The portion of <i>str</i> specified by the <i>offset</i> and
     *                      <i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
     *                      characters long, <b>FALSE</b> will be returned.
     */
    public static function substr(
        string $str,
        int $offset = 0,
        ?int $length = null,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        // empty string
        if ($str === '' || $length === 0) {
            return '';
        }

        if ($clean_utf8) {
            // iconv and mbstring are not tolerant to invalid encoding
            // further, their behaviour is inconsistent with that of PHP's substr
            $str = self::clean($str);
        }

        // whole string
        if (!$offset && $length === null) {
            return $str;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        //
        // fallback via mbstring
        //

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true && $encoding === self::UTF8) {
            if ($length === null) {
                return \mb_substr($str, $offset);
            }

            return \mb_substr($str, $offset, $length);
        }

        //
        // fallback for binary || ascii only
        //

        if (
            $encoding === self::CP850
            ||
            $encoding === self::ASCII
        ) {
            if ($length === null) {
                return \substr($str, $offset);
            }

            return \substr($str, $offset, $length);
        }

        // otherwise we need the string-length
        $str_length = 0;
        if (
            $offset
            ||
            $length === null /* @phpstan-ignore-line | can be NULL here?! */
        ) {
            $str_length = self::strlen($str, $encoding);
        }

        // e.g.: invalid chars + mbstring not installed
        if ($str_length === false) {
            return false;
        }

        // empty string
        if ($offset === $str_length && !$length) {
            return '';
        }

        // impossible
        if ($offset && $offset > $str_length) {
            return '';
        }

        $length = $length ?? $str_length;

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::substr() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        //
        // fallback via intl
        //

        if (
            $encoding === self::UTF8 // INFO: "grapheme_substr()" can't handle other encodings
            &&
            $offset >= 0 // grapheme_substr() can't handle negative offset
            &&
            self::$SUPPORT[self::FEATURE_INTL] === true
        ) {
            $return_tmp = \grapheme_substr($str, $offset, $length);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback via iconv
        //

        if (
            $length >= 0 // "iconv_substr()" can't handle negative length
            &&
            self::$SUPPORT[self::FEATURE_ICONV] === true
        ) {
            $return_tmp = \iconv_substr($str, $offset, $length);
            if ($return_tmp !== false) {
                return $return_tmp;
            }
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($str)) {
            return \substr($str, $offset, $length);
        }

        //
        // fallback via vanilla php
        //

        // split to array, and remove invalid characters
        // &&
        // extract relevant part, and join to make sting again
        return \implode('', \array_slice(self::strSplit($str), $offset, $length));
    }

    /**
     * Binary-safe comparison of two strings from an offset, up to a length of characters.
     *
     * EXAMPLE: <code>
     * UTF8::substr_compare("○●◎\r", '●◎', 0, 2); // -1
     * UTF8::substr_compare("○●◎\r", '◎●', 1, 2); // 1
     * UTF8::substr_compare("○●◎\r", '●◎', 1, 2); // 0
     * </code>
     *
     * @param string   $str1               <p>The main string being compared.</p>
     * @param string   $str2               <p>The secondary string being compared.</p>
     * @param int      $offset             [optional] <p>The start position for the comparison. If negative, it starts
     *                                     counting from the end of the string.</p>
     * @param int|null $length             [optional] <p>The length of the comparison. The default value is the largest
     *                                     of the length of the str compared to the length of main_str less the
     *                                     offset.</p>
     * @param bool     $case_insensitivity [optional] <p>If case_insensitivity is TRUE, comparison is case
     *                                     insensitive.</p>
     * @param string   $encoding           [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return int
     *             <strong>&lt; 0</strong> if str1 is less than str2;<br>
     *             <strong>&gt; 0</strong> if str1 is greater than str2,<br>
     *             <strong>0</strong> if they are equal
     */
    public static function substr_compare(
        string $str1,
        string $str2,
        int $offset = 0,
        ?int $length = null,
        bool $case_insensitivity = false,
        string $encoding = self::UTF8
    ): int {
        if (
            $offset !== 0
            ||
            $length !== null
        ) {
            if ($encoding === self::UTF8) {
                if ($length === null) {
                    $str1 = (string) \mb_substr($str1, $offset);
                } else {
                    $str1 = (string) \mb_substr($str1, $offset, $length);
                }
                $str2 = (string) \mb_substr($str2, 0, (int) self::strlen($str1));
            } else {
                $encoding = self::normalize_encoding($encoding, self::UTF8);

                $str1 = (string) self::substr($str1, $offset, $length, $encoding);
                $str2 = (string) self::substr($str2, 0, (int) self::strlen($str1), $encoding);
            }
        }

        if ($case_insensitivity) {
            return self::strcasecmp($str1, $str2, $encoding);
        }

        return self::strcmp($str1, $str2);
    }

    /**
     * Count the number of substring occurrences.
     *
     * EXAMPLE: <code>UTF8::substr_count('中文空白', '文空', 1, 2); // 1</code>
     *
     * @see http://php.net/manual/en/function.substr-count.php
     *
     * @param string   $haystack   <p>The string to search in.</p>
     * @param string   $needle     <p>The substring to search for.</p>
     * @param int      $offset     [optional] <p>The offset where to start counting.</p>
     * @param int|null $length     [optional] <p>
     *                             The maximum length after the specified offset to search for the
     *                             substring. It outputs a warning if the offset plus the length is
     *                             greater than the haystack length.
     *                             </p>
     * @param string   $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool     $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>This functions returns an integer or false if there isn't a string.</p>
     */
    public static function substr_count(
        string $haystack,
        string $needle,
        int $offset = 0,
        ?int $length = null,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ) {
        if ($needle === '') {
            return false;
        }

        if ($haystack === '' || $length === 0) {
            return 0;
        }

        if ($encoding !== self::UTF8 && $encoding !== self::CP850) {
            $encoding = self::normalize_encoding($encoding, self::UTF8);
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $needle = self::clean($needle);
            $haystack = self::clean($haystack);
        }

        if ($offset || $length > 0) {
            if ($length === null) {
                $length_tmp = self::strlen($haystack, $encoding);
                if ($length_tmp === false) {
                    return false;
                }
                $length = $length_tmp;
            }

            if ($encoding === self::UTF8) {
                $haystack = (string) \mb_substr($haystack, $offset, $length);
            } else {
                $haystack = (string) \mb_substr($haystack, $offset, $length, $encoding);
            }
        }

        if (
            $encoding !== self::UTF8
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING] === false
        ) {
            /**
             * @psalm-suppress ImpureFunctionCall - this is only a warning
             */
            \trigger_error('UTF8::substr_count() without mbstring cannot handle "' . $encoding . '" encoding', \E_USER_WARNING);
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($encoding === self::UTF8) {
                return \mb_substr_count($haystack, $needle);
            }

            return \mb_substr_count($haystack, $needle, $encoding);
        }

        \preg_match_all('/' . \preg_quote($needle, '/') . '/us', $haystack, $matches, \PREG_SET_ORDER);

        return \count($matches);
    }

    /**
     * Count the number of substring occurrences.
     *
     * @param string   $haystack <p>
     *                           The string being checked.
     *                           </p>
     * @param string   $needle   <p>
     *                           The string being found.
     *                           </p>
     * @param int      $offset   [optional] <p>
     *                           The offset where to start counting
     *                           </p>
     * @param int|null $length   [optional] <p>
     *                           The maximum length after the specified offset to search for the
     *                           substring. It outputs a warning if the offset plus the length is
     *                           greater than the haystack length.
     *                           </p>
     *
     * @psalm-pure
     *
     * @return false|int
     *                   <p>The number of times the
     *                   needle substring occurs in the
     *                   haystack string.</p>
     */
    public static function substr_count_in_byte(
        string $haystack,
        string $needle,
        int $offset = 0,
        ?int $length = null
    ) {
        if ($haystack === '' || $needle === '') {
            return 0;
        }

        if (
            ($offset || $length !== null)
            &&
            self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true
        ) {
            if ($length === null) {
                $length_tmp = self::strlen($haystack);
                if ($length_tmp === false) {
                    return false;
                }
                $length = $length_tmp;
            }

            if (
                (
                    $length !== 0
                    &&
                    $offset !== 0
                )
                &&
                ($length + $offset) <= 0
                &&
                \PHP_VERSION_ID < 71000 // output from "substr_count()" have changed in PHP 7.1
            ) {
                return false;
            }

            /** @var false|string $haystack_tmp - needed for PhpStan (stubs error) */
            $haystack_tmp = \substr($haystack, $offset, $length);
            if ($haystack_tmp === false) {
                $haystack_tmp = '';
            }
            $haystack = (string) $haystack_tmp;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_substr_count($haystack, $needle, self::CP850); // 8-BIT
        }

        if ($length === null) {
            return \substr_count($haystack, $needle, $offset);
        }

        return \substr_count($haystack, $needle, $offset, $length);
    }

    /**
     * Returns the number of occurrences of a substring in the given string.
     * By default, the comparison is case-sensitive but can be made insensitive.
     *
     * @param string $string         The input string.
     * @param string $substring      The substring to search for.
     * @param bool   $caseSensitive  Whether to enforce case-sensitivity (default: true).
     * @param string $encoding       The character encoding (default: UTF-8).
     *
     * @psalm-pure
     *
     * @return int The number of occurrences.
     *
     * @phpstan-return 0|positive-int
     */
    public static function countSubstring(
        string $string,
        string $substring,
        bool $caseSensitive = true,
        string $encoding = self::UTF8
    ): int {
        if ($string === '' || $substring === '') {
            return 0;
        }

        if ($encoding !== self::UTF8) {
            $encoding = self::normalizeEncoding($encoding, self::UTF8);
        }

        return $caseSensitive
            ? (int) mb_substr_count($string, $substring, $encoding)
            : (int) mb_substr_count(
                self::toCaseFold($string, $encoding),
                self::toCaseFold($substring, $encoding),
                $encoding
            );
    }

    /**
     * Normalizes the given encoding to a fallback encoding if necessary.
     *
     * @param string $encoding The input encoding.
     * @param string $fallback The fallback encoding.
     * @return string The normalized encoding.
     */
    private static function normalizeEncoding(string $encoding, string $fallback): string
    {
        return in_array(strtoupper($encoding), mb_list_encodings(), true) ? $encoding : $fallback;
    }

    /**
     * Converts a string to a case-folded version for case-insensitive comparison.
     *
     * @param string $string   The input string.
     * @param string $encoding The character encoding.
     * @return string The case-folded string.
     */
    private static function toCaseFold(string $string, string $encoding): string
    {
        return mb_strtoupper($string, $encoding);
    }

    /**
     * Removes a prefix ($needle) from the beginning of the string ($haystack), case-insensitive.
     *
     * EXMAPLE: <code>
     * UTF8::substr_ileft('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
     * UTF8::substr_ileft('ΚόσμεMiddleEnd', 'κόσμε'); // 'MiddleEnd'
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Return the sub-string.</p>
     */
    public static function substr_ileft(string $haystack, string $needle): string
    {
        if ($haystack === '') {
            return '';
        }

        if ($needle === '') {
            return $haystack;
        }

        if (self::str_istarts_with($haystack, $needle)) {
            $haystack = (string) \mb_substr($haystack, (int) self::strlen($needle));
        }

        return $haystack;
    }

    /**
     * Get part of a string process in bytes.
     *
     * @param string   $str    <p>The string being checked.</p>
     * @param int      $offset <p>The first position used in str.</p>
     * @param int|null $length [optional] <p>The maximum length of the returned string.</p>
     *
     * @psalm-pure
     *
     * @return false|string
     *                      <p>The portion of <i>str</i> specified by the <i>offset</i> and
     *                      <i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
     *                      characters long, <b>FALSE</b> will be returned.</p>
     */
    public static function substr_in_byte(string $str, int $offset = 0, ?int $length = null)
    {
        // empty string
        if ($str === '' || $length === 0) {
            return '';
        }

        // whole string
        if (!$offset && $length === null) {
            return $str;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING_OVERLOAD] === true) {
            // "mb_" is available if overload is used, so use it ...
            return \mb_substr($str, $offset, $length, self::CP850); // 8-BIT
        }

        return \substr($str, $offset, $length ?? 2147483647);
    }

    /**
     * Removes a suffix ($needle) from the end of the string ($haystack), case-insensitive.
     *
     * EXAMPLE: <code>
     * UTF8::substr_iright('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
     * UTF8::substr_iright('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddle'
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Return the sub-string.<p>
     */
    public static function substr_iright(string $haystack, string $needle): string
    {
        if ($haystack === '') {
            return '';
        }

        if ($needle === '') {
            return $haystack;
        }

        if (self::str_iends_with($haystack, $needle)) {
            $haystack = (string) \mb_substr($haystack, 0, (int) self::strlen($haystack) - (int) self::strlen($needle));
        }

        return $haystack;
    }

    /**
     * Removes a prefix ($needle) from the beginning of the string ($haystack).
     *
     * EXAMPLE: <code>
     * UTF8::substr_left('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
     * UTF8::substr_left('ΚόσμεMiddleEnd', 'κόσμε'); // 'ΚόσμεMiddleEnd'
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Return the sub-string.</p>
     */
    public static function substr_left(string $haystack, string $needle): string
    {
        if ($haystack === '') {
            return '';
        }

        if ($needle === '') {
            return $haystack;
        }

        if (self::str_starts_with($haystack, $needle)) {
            $haystack = (string) \mb_substr($haystack, (int) self::strlen($needle));
        }

        return $haystack;
    }

    /**
     * Replace text within a portion of a string.
     *
     * EXAMPLE: <code>UTF8::substr_replace(array('Iñtërnâtiônàlizætiøn', 'foo'), 'æ', 1); // array('Iæñtërnâtiônàlizætiøn', 'fæoo')</code>
     *
     * source: https://gist.github.com/stemar/8287074
     *
     * @param string|string[] $str         <p>The input string or an array of stings.</p>
     * @param string|string[] $replacement <p>The replacement string or an array of stings.</p>
     * @param int|int[]       $offset      <p>
     *                                     If start is positive, the replacing will begin at the start'th offset
     *                                     into string.
     *                                     <br><br>
     *                                     If start is negative, the replacing will begin at the start'th character
     *                                     from the end of string.
     *                                     </p>
     * @param int|int[]|null  $length      [optional] <p>If given and is positive, it represents the length of the
     *                                     portion of string which is to be replaced. If it is negative, it
     *                                     represents the number of characters from the end of string at which to
     *                                     stop replacing. If it is not given, then it will default to strlen(
     *                                     string ); i.e. end the replacing at the end of string. Of course, if
     *                                     length is zero then this function will have the effect of inserting
     *                                     replacement into string at the given start offset.</p>
     * @param string          $encoding    [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string|string[]
     *                         <p>The result string is returned. If string is an array then array is returned.</p>
     *
     * @template TSubstrReplace string|string[]
     * @phpstan-param TSubstrReplace $str
     * @phpstan-return TSubstrReplace
     */
    public static function substr_replace(
        $str,
        $replacement,
        $offset,
        $length = null,
        string $encoding = self::UTF8
    ) {
        if (\is_array($str)) {
            $num = \count($str);

            // the replacement
            if (\is_array($replacement)) {
                $replacement = \array_slice($replacement, 0, $num);
            } else {
                $replacement = \array_pad([$replacement], $num, $replacement);
            }

            // the offset
            if (\is_array($offset)) {
                $offset = \array_slice($offset, 0, $num);
                foreach ($offset as &$value_tmp) {
                    $value_tmp = (int) $value_tmp === $value_tmp ? $value_tmp : 0;
                }
                unset($value_tmp);
            } else {
                $offset = \array_pad([$offset], $num, $offset);
            }

            // the length
            if ($length === null) {
                $length = \array_fill(0, $num, 0);
            } elseif (\is_array($length)) {
                $length = \array_slice($length, 0, $num);
                foreach ($length as &$value_tmp_V2) {
                    $value_tmp_V2 = (int) $value_tmp_V2 === $value_tmp_V2 ? $value_tmp_V2 : $num;
                }
                unset($value_tmp_V2);
            } else {
                $length = \array_pad([$length], $num, $length);
            }

            // recursive call
            /** @phpstan-ignore-next-line - phpstan currently can't handle recursive calls */
            return \array_map([self::class, 'substr_replace'], $str, $replacement, $offset, $length);
        }

        if (\is_array($replacement)) {
            if ($replacement !== []) {
                $replacement = $replacement[0];
            } else {
                $replacement = '';
            }
        }

        // init
        $str = (string) $str;
        $replacement = (string) $replacement;

        if (\is_array($length)) {
            throw new \InvalidArgumentException('Parameter "$length" can only be an array, if "$str" is also an array.');
        }

        if (\is_array($offset)) {
            throw new \InvalidArgumentException('Parameter "$offset" can only be an array, if "$str" is also an array.');
        }

        if ($str === '') {
            return $replacement;
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            $string_length = (int) self::strlen($str, $encoding);

            if ($offset < 0) {
                $offset = (int) \max(0, $string_length + $offset);
            } elseif ($offset > $string_length) {
                $offset = $string_length;
            }

            if ($length !== null && $length < 0) {
                $length = (int) \max(0, $string_length - $offset + $length);
            } elseif ($length === null || $length > $string_length) {
                $length = $string_length;
            }

            if (($offset + $length) > $string_length) {
                $length = $string_length - $offset;
            }

            return ((string) \mb_substr($str, 0, $offset, $encoding)) .
                   $replacement .
                   ((string) \mb_substr($str, $offset + $length, $string_length - $offset - $length, $encoding));
        }

        //
        // fallback for ascii only
        //

        if (ASCII::isAscii($str)) {
            return ($length === null) ?
                \substr_replace($str, $replacement, $offset) :
                \substr_replace($str, $replacement, $offset, $length);
        }

        //
        // fallback via vanilla php
        //

        \preg_match_all('/./us', $str, $str_matches);
        \preg_match_all('/./us', $replacement, $replacement_matches);

        if ($length === null) {
            $length_tmp = self::strlen($str, $encoding);
            if ($length_tmp === false) {
                // e.g.: non mbstring support + invalid chars
                return '';
            }
            $length = $length_tmp;
        }

        \array_splice($str_matches[0], $offset, $length, $replacement_matches[0]);

        return \implode('', $str_matches[0]);
    }

    /**
     * Removes a suffix ($needle) from the end of the string ($haystack).
     *
     * EXAMPLE: <code>
     * UTF8::substr_right('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
     * UTF8::substr_right('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddleΚόσμε'
     * </code>
     *
     * @param string $haystack <p>The string to search in.</p>
     * @param string $needle   <p>The substring to search for.</p>
     * @param string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Return the sub-string.</p>
     */
    public static function substr_right(
        string $haystack,
        string $needle,
        string $encoding = self::UTF8
    ): string {
        if ($haystack === '') {
            return '';
        }

        if ($needle === '') {
            return $haystack;
        }

        if (
            $encoding === self::UTF8
            &&
            \substr($haystack, -\strlen($needle)) === $needle
        ) {
            return (string) \mb_substr($haystack, 0, (int) \mb_strlen($haystack) - (int) \mb_strlen($needle));
        }

        if (\substr($haystack, -\strlen($needle)) === $needle) {
            return (string) self::substr(
                $haystack,
                0,
                (int) self::strlen($haystack, $encoding) - (int) self::strlen($needle, $encoding),
                $encoding
            );
        }

        return $haystack;
    }

    /**
     * Returns a case swapped version of the string.
     *
     * EXAMPLE: <code>UTF8::swapCase('déJÀ σσς iıII'); // 'DÉjà ΣΣΣ IIii'</code>
     *
     * @param string $str        <p>The input string.</p>
     * @param string $encoding   [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool   $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>Each character's case swapped.</p>
     */
    public static function swapCase(string $str, string $encoding = self::UTF8, bool $clean_utf8 = false): string
    {
        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        if ($encoding === self::UTF8) {
            return (string) (\mb_strtolower($str) ^ \mb_strtoupper($str) ^ $str);
        }

        return (string) (self::strtolower($str, $encoding) ^ self::strtoupper($str, $encoding) ^ $str);
    }

    /**
     * Checks whether symfony-polyfills are used.
     *
     * @psalm-pure
     *
     * @return bool
     *              <p><strong>true</strong> if in use, <strong>false</strong> otherwise</p>
     *
     * @internal <p>Please do not use it anymore, we will make is private in next major version.</p>
     */
    public static function symfony_polyfill_used(): bool
    {
        // init
        $return = false;

        $return_tmp = \extension_loaded('mbstring');
        if (!$return_tmp && \function_exists('mb_strlen')) {
            $return = true;
        }

        $return_tmp = \extension_loaded('iconv');
        if (!$return_tmp && \function_exists('iconv')) {
            $return = true;
        }

        return $return;
    }

    /**
     * @param string $str
     * @param int    $tab_length
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function tabs_to_spaces(string $str, int $tab_length = 4): string
    {
        if ($tab_length === 4) {
            $spaces = '    ';
        } elseif ($tab_length === 2) {
            $spaces = '  ';
        } else {
            $spaces = \str_repeat(' ', $tab_length);
        }

        return \str_replace("\t", $spaces, $str);
    }

    /**
     * Converts the first character of each word in the string to uppercase
     * and all other chars to lowercase.
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A string with all characters of $str being title-cased.</p>
     */
    public static function titlecase(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        if (
            $lang === null
            &&
            !$try_to_keep_the_string_length
        ) {
            if ($encoding === self::UTF8) {
                return \mb_convert_case($str, \MB_CASE_TITLE);
            }

            $encoding = self::normalize_encoding($encoding, self::UTF8);

            return \mb_convert_case($str, \MB_CASE_TITLE, $encoding);
        }

        return self::str_titleize(
            $str,
            null,
            $encoding,
            false,
            $lang,
            $try_to_keep_the_string_length,
            false
        );
    }

    /**
     * Convert a string into ASCII.
     *
     * EXAMPLE: <code>UTF8::to_ascii('déjà σσς iıii'); // 'deja sss iiii'</code>
     *
     * @param string $str     <p>The input string.</p>
     * @param string $unknown [optional] <p>Character use if character unknown. (default is ?)</p>
     * @param bool   $strict  [optional] <p>Use "transliterator_transliterate()" from PHP-Intl | WARNING: bad
     *                        performance</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function to_ascii(
        string $str,
        string $unknown = '?',
        bool $strict = false
    ): string {
        return ASCII::to_transliterate($str, $unknown, $strict);
    }

    /**
     * @param bool|float|int|string $str
     *
     * @psalm-pure
     *
     * @return bool
     */
    public static function to_boolean($str): bool
    {
        // init
        $str = (string) $str;

        if ($str === '') {
            return false;
        }

        // Info: http://php.net/manual/en/filter.filters.validate.php
        $map = [
            'true'  => true,
            '1'     => true,
            'on'    => true,
            'yes'   => true,
            'false' => false,
            '0'     => false,
            'off'   => false,
            'no'    => false,
        ];

        if (isset($map[$str])) {
            return $map[$str];
        }

        $key = \strtolower($str);
        if (isset($map[$key])) {
            return $map[$key];
        }

        if (\is_numeric($str)) {
            return ((float) $str) > 0;
        }

        return (bool) \trim($str);
    }

    /**
     * Convert given string to safe filename (and keep string case).
     *
     * @param string $str
     * @param bool   $use_transliterate No transliteration, conversion etc. is done by default - unsafe characters are
     *                                  simply replaced with hyphen.
     * @param string $fallback_char
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function to_filename(
        string $str,
        bool $use_transliterate = false,
        string $fallback_char = '-'
    ): string {
        return ASCII::to_filename(
            $str,
            $use_transliterate,
            $fallback_char
        );
    }

    /**
     * Converts a string to "ISO-8859" encoding (Latin-1).
     *
     * This function converts UTF-8 encoded text into ISO-8859-1, replacing any non-Latin-1
     * characters with their closest equivalent or a placeholder.
     *
     * EXAMPLE:
     * ```php
     * UTF8::toUtf8(UTF8::toIso8859('  -ABC-中文空白-  ')); // '  -ABC-????-  '
     * ```
     *
     * @param string|string[] $str The input string or array of strings.
     *
     * @psalm-pure
     *
     * @return string|string[] The converted ISO-8859 encoded string or array of strings.
     *
     * @template TToIso8859 as string|string[]
     * @phpstan-param TToIso8859 $str
     * @phpstan-return (TToIso8859 is string ? string : string[])
     */
    public static function toIso8859(string|array $str): string|array
    {
        if (is_array($str)) {
            foreach ($str as &$v) {
                $v = self::toIso8859($v);
            }
            return $str;
        }

        return $str === '' ? '' : self::utf8Decode($str);
    }


    /**
     * This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.
     *
     * <ul>
     * <li>It decodes UTF-8 codepoints and Unicode escape sequences.</li>
     * <li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
     * <li>WARNING: It does not remove invalid UTF-8 characters, so you may need to use "UTF8::clean()" for this
     * case.</li>
     * </ul>
     *
     * EXAMPLE: <code>UTF8::toUtf8(["\u0063\u0061\u0074"]); // array('cat')</code>
     *
     * @param string|string[] $str                        The string or array of strings to convert.
     * @param bool            $decodeHtmlEntityToUtf8    Set to true if you need to decode HTML entities.
     *
     * @psalm-pure
     *
     * @return string|string[]                            The UTF-8 encoded string or array of strings.
     *
     * @template TToUtf8 as string|string[]
     * @phpstan-param TToUtf8 $str
     * @phpstan-return (TToUtf8 is string ? string : string[])
     */
    public static function toUtf8($str, bool $decodeHtmlEntityToUtf8 = false)
    {
        // Handle array input
        if (is_array($str)) {
            return array_map(
                fn($v) => self::toUtf8String($v, $decodeHtmlEntityToUtf8), 
                $str
            );
        }

        // Handle string input
        assert(is_string($str));

        return self::toUtf8String($str, $decodeHtmlEntityToUtf8);
    }

    /**
     * This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.
     *
     * <ul>
     * <li>It decodes UTF-8 codepoints and Unicode escape sequences.</li>
     * <li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
     * <li>WARNING: It does not remove invalid UTF-8 characters, so you may need to use "UTF8::clean()" for this case.</li>
     * </ul>
     *
     * EXAMPLE: <code>UTF8::toUtf8String("\u0063\u0061\u0074"); // 'cat'</code>
     *
     * @param string $str                        The string to convert.
     * @param bool   $decodeHtmlEntityToUtf8    Set to true if you need to decode HTML entities.
     *
     * @psalm-pure
     *
     * @return string                            The UTF-8 encoded string.
     */
    public static function toUtf8String(string $str, bool $decodeHtmlEntityToUtf8 = false): string
    {
        if ($str === '') {
            return $str;
        }

        $max = strlen($str);
        $buf = '';

        // Optimized loop to handle UTF-8 characters
        for ($i = 0; $i < $max; ++$i) {
            $c1 = $str[$i];

            if ($c1 >= "\xC0") { // Non-UTF-8 character, needs conversion
                $nextChars = substr($str, $i + 1, 3); // Read the next 3 characters in one go to avoid multiple index checks

                // Check for 2, 3, or 4 byte UTF-8 sequences
                if (($c1 <= "\xDF" && self::isValidUtf8($nextChars, 1)) || 
                    ($c1 >= "\xE0" && $c1 <= "\xEF" && self::isValidUtf8($nextChars, 2)) || 
                    ($c1 >= "\xF0" && $c1 <= "\xF7" && self::isValidUtf8($nextChars, 3))) {
                    $buf .= $c1 . $nextChars; 
                    $i += strlen($nextChars); 
                } else {
                    $buf .= self::toUtf8ConvertHelper($c1); // Convert invalid UTF-8
                }
            } elseif (($c1 & "\xC0") === "\x80") { // Continuation byte, needs conversion
                $buf .= self::toUtf8ConvertHelper($c1);
            } else { // Valid ASCII or single-byte UTF-8
                $buf .= $c1;
            }
        }

        // Decode unicode escape sequences and surrogate pairs
        $buf = preg_replace_callback(
            '/\\\\u([dD][89abAB][0-9a-fA-F]{2})\\\\u([dD][cdefCDEF][\da-fA-F]{2})|\\\\u([0-9a-fA-F]{4})/',
            static function (array $matches): string {
                if (isset($matches[3])) {
                    $cp = (int) hexdec($matches[3]);
                } else {
                    // Surrogate pair decoding
                    $cp = ((int) hexdec($matches[1]) << 10)
                        + (int) hexdec($matches[2])
                        + 0x10000
                        - (0xD800 << 10)
                        - 0xDC00;
                }

                return self::decodeCodepoint($cp);
            },
            $buf
        );

        // Ensure buffer is not null or empty
        if ($buf === null) {
            return '';
        }

        // Decode HTML entities if required
        if ($decodeHtmlEntityToUtf8) {
            $buf = self::htmlEntityDecode($buf);
        }

        return $buf;
    }

    /**
     * Checks if the given substring represents a valid UTF-8 sequence of the specified length.
     *
     * @param string $str      The substring to check.
     * @param int    $length   The expected length of the UTF-8 sequence.
     *
     * @return bool
     */
    private static function isValidUtf8(string $str, int $length): bool
    {
        // Validate based on expected byte length
        return strlen($str) === $length && preg_match('/^[\x80-\xBF]+$/', $str);
    }

    /**
     * Decodes a UTF-8 codepoint into its corresponding UTF-8 character.
     *
     * @param int $cp The codepoint to decode.
     *
     * @return string The UTF-8 encoded character.
     */
    private static function decodeCodepoint(int $cp): string
    {
        if ($cp < 0x80) {
            return chr($cp);
        }

        if ($cp < 0xA0) {
            return chr(0xC0 | $cp >> 6) . chr(0x80 | $cp & 0x3F);
        }

        return self::decimalToChr($cp);
    }

    /**
     * Returns the given string as an integer, or null if the string isn't numeric.
     *
     * @param string $str
     *
     * @psalm-pure
     *
     * @return int|null
     *                  <p>null if the string isn't numeric</p>
     */
    public static function to_int(string $str)
    {
        if (\is_numeric($str)) {
            return (int) $str;
        }

        return null;
    }

    /**
     * Returns the given input as string, or null if the input isn't int|float|string
     * and do not implement the "__toString()" method.
     *
     * @param float|int|object|string|null $input
     *
     * @psalm-pure
     *
     * @return string|null
     *                     <p>null if the input isn't int|float|string and has no "__toString()" method</p>
     */
    public static function to_string($input)
    {
        if ($input === null) {
            return null;
        }

        $input_type = \gettype($input);

        if (
            $input_type === 'string'
            ||
            $input_type === 'integer'
            ||
            $input_type === 'float'
            ||
            $input_type === 'double'
        ) {
            /* @phpstan-ignore-next-line | "gettype" is not supported by phpstan?! */
            return (string) $input;
        }

        /** @phpstan-ignore-next-line - "gettype": FP? */
        if ($input_type === 'object' && \method_exists($input, '__toString')) {
            return (string) $input;
        }

        return null;
    }

    /**
     * Strip whitespace or other characters from the beginning and end of a UTF-8 string.
     *
     * INFO: This is slower then "trim()"
     *
     * We can only use the original-function, if we use <= 7-Bit in the string / chars
     * but the check for ASCII (7-Bit) cost more time, then we can safe here.
     *
     * EXAMPLE: <code>UTF8::trim('   -ABC-中文空白-  '); // '-ABC-中文空白-'</code>
     *
     * @param string      $str   <p>The string to be trimmed</p>
     * @param string|null $chars [optional] <p>Optional characters to be stripped</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The trimmed string.</p>
     */
    public static function trim(string $str = '', ?string $chars = null): string
    {
        if ($str === '') {
            return '';
        }

        if (self::$SUPPORT[self::FEATURE_MBSTRING] === true) {
            if ($chars !== null) {
                /** @noinspection PregQuoteUsageInspection */
                $chars = \preg_quote($chars);
                $pattern = "^[{$chars}]+|[{$chars}]+\$";
            } else {
                $pattern = '^[\\s]+|[\\s]+$';
            }

            return (string) \mb_ereg_replace($pattern, '', $str);
        }

        if ($chars !== null) {
            $chars = \preg_quote($chars, '/');
            $pattern = "^[{$chars}]+|[{$chars}]+\$";
        } else {
            $pattern = '^[\\s]+|[\\s]+$';
        }

        return self::regexReplace($str, $pattern, '');
    }

    /**
     * Makes string's first char uppercase.
     *
     * EXAMPLE: <code>UTF8::ucfirst('ñtërnâtiônàlizætiøn foo'); // 'Ñtërnâtiônàlizætiøn foo'</code>
     *
     * @param string      $str                           <p>The input string.</p>
     * @param string      $encoding                      [optional] <p>Set the charset for e.g. "mb_" function</p>
     * @param bool        $clean_utf8                    [optional] <p>Remove non UTF-8 chars from the string.</p>
     * @param string|null $lang                          [optional] <p>Set the language for special cases: az, el, lt,
     *                                                   tr</p>
     * @param bool        $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
     *                                                   -> ß</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The resulting string with with char uppercase.</p>
     */
    public static function ucfirst(
        string $str,
        string $encoding = self::UTF8,
        bool $clean_utf8 = false,
        ?string $lang = null,
        bool $try_to_keep_the_string_length = false
    ): string {
        if ($str === '') {
            return '';
        }

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        $use_mb_functions = $lang === null && !$try_to_keep_the_string_length;

        if ($encoding === self::UTF8) {
            $str_part_two = (string) \mb_substr($str, 1);

            if ($use_mb_functions) {
                $str_part_one = \mb_strtoupper(
                    (string) \mb_substr($str, 0, 1)
                );
            } else {
                $str_part_one = self::strtoupper(
                    (string) \mb_substr($str, 0, 1),
                    $encoding,
                    false,
                    $lang,
                    $try_to_keep_the_string_length
                );
            }
        } else {
            $encoding = self::normalize_encoding($encoding, self::UTF8);

            $str_part_two = (string) self::substr($str, 1, null, $encoding);

            if ($use_mb_functions) {
                $str_part_one = \mb_strtoupper(
                    (string) \mb_substr($str, 0, 1, $encoding),
                    $encoding
                );
            } else {
                $str_part_one = self::strtoupper(
                    (string) self::substr($str, 0, 1, $encoding),
                    $encoding,
                    false,
                    $lang,
                    $try_to_keep_the_string_length
                );
            }
        }

        return $str_part_one . $str_part_two;
    }

    /**
     * Uppercase for all words in the string.
     *
     * EXAMPLE: <code>UTF8::ucwords('iñt ërn âTi ônà liz æti øn'); // 'Iñt Ërn ÂTi Ônà Liz Æti Øn'</code>
     *
     * @param string   $str        <p>The input string.</p>
     * @param string[] $exceptions [optional] <p>Exclusion for some words.</p>
     * @param string   $char_list  [optional] <p>Additional chars that contains to words and do not start a new
     *                             word.</p>
     * @param string   $encoding   [optional] <p>Set the charset.</p>
     * @param bool     $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function ucwords(
        string $str,
        array $exceptions = [],
        string $char_list = '',
        string $encoding = self::UTF8,
        bool $clean_utf8 = false
    ): string {
        if (!$str) {
            return '';
        }

        // INFO: mb_convert_case($str, MB_CASE_TITLE);
        // -> MB_CASE_TITLE didn't only uppercase the first letter, it also lowercase all other letters

        if ($clean_utf8) {
            // "mb_strpos()" and "iconv_strpos()" returns wrong position,
            // if invalid characters are found in $haystack before $needle
            $str = self::clean($str);
        }

        $use_php_default_functions = !(bool) ($char_list . \implode('', $exceptions));

        if (
            $use_php_default_functions
            &&
            ASCII::isAscii($str)
        ) {
            return \ucwords($str);
        }

        $words = self::str_to_words($str, $char_list);
        $use_exceptions = $exceptions !== [];

        $words_str = '';
        foreach ($words as &$word) {
            if (!$word) {
                continue;
            }

            if (
                !$use_exceptions
                ||
                !\in_array($word, $exceptions, true)
            ) {
                $words_str .= self::ucfirst($word, $encoding);
            } else {
                $words_str .= $word;
            }
        }

        return $words_str;
    }

    /**
     * Multi decode HTML entity + fix urlencoded-win1252-chars.
     *
     * EXAMPLE: <code>UTF8::urldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest test'</code>
     *
     * e.g:
     * 'test+test'                     => 'test test'
     * 'D&#252;sseldorf'               => 'Düsseldorf'
     * 'D%FCsseldorf'                  => 'Düsseldorf'
     * 'D&#xFC;sseldorf'               => 'Düsseldorf'
     * 'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
     * 'DÃ¼sseldorf'                   => 'Düsseldorf'
     * 'D%C3%BCsseldorf'               => 'Düsseldorf'
     * 'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
     * 'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'
     *
     * @param string $str          <p>The input string.</p>
     * @param bool   $multi_decode <p>Decode as often as possible.</p>
     *
     * @psalm-pure
     *
     * @return string
     *
     * @template T as string
     * @phpstan-param T $str
     * @phpstan-return (T is non-empty-string ? non-empty-string : string)
     */
    public static function urldecode(string $str, bool $multi_decode = true): string
    {
        if ($str === '') {
            return '';
        }

        $str = self::urlDecodeUnicodeHelper($str);

        if ($multi_decode) {
            do {
                $str_compare = $str;

                /**
                 * @psalm-suppress PossiblyInvalidArgument
                 */
                $str = \urldecode(
                    self::htmlEntityDecode(
                        self::toUtf8($str),
                        \ENT_QUOTES | \ENT_HTML5
                    )
                );
            } while ($str_compare !== $str);
        } else {
            /**
             * @psalm-suppress PossiblyInvalidArgument
             */
            $str = \urldecode(
                self::htmlEntityDecode(
                    self::toUtf8($str),
                    \ENT_QUOTES | \ENT_HTML5
                )
            );
        }

        return self::fixSimpleUtf8($str);
    }

    /**
     * Decodes a UTF-8 string to ISO-8859-1.
     *
     * Converts UTF-8 characters to their closest ISO-8859-1 equivalents.
     * Unsupported characters are replaced with `?`, unless `$keepUtf8Chars` is true.
     *
     * EXAMPLE:
     * ```php
     * UTF8::encode('UTF-8', UTF8::utf8Decode('-ABC-中文空白-')); // '-ABC-????-'
     * ```
     *
     * @param string $str The input UTF-8 encoded string.
     * @param bool   $keepUtf8Chars Whether to keep the original string if decoding loses characters.
     *
     * @psalm-pure
     *
     * @return string The decoded ISO-8859-1 string.
     */
    public static function utf8Decode(string $str, bool $keepUtf8Chars = false): string
    {
        if ($str === '') {
            return '';
        }

        $originalStr = $str;
        $length = strlen($str);

        if (self::$ORD === null) {
            self::$ORD = self::getData('ord');
        }

        if (self::$CHR === null) {
            self::$CHR = self::getData('chr');
        }

        $noCharFound = '?';
        $decodedStr = '';

        for ($i = 0, $j = 0; $i < $length; ++$i, ++$j) {
            switch ($str[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (self::$ORD[$str[$i] & "\x1F"] << 6) | self::$ORD[$str[++$i] & "\x3F"];
                    $decodedStr .= $c < 256 ? self::$CHR[$c] : $noCharFound;
                    break;

                case "\xF0":
                    ++$i; // Skip an extra byte

                case "\xE0":
                    $decodedStr .= $noCharFound;
                    $i += 2;
                    break;

                default:
                    $decodedStr .= $str[$i];
            }
        }

        if ($keepUtf8Chars && strlen($decodedStr) >= strlen($originalStr)) {
            return $originalStr;
        }

        return $decodedStr;
    }

    /**
     * Encodes an ISO-8859-1 string to UTF-8.
     *
     * Converts characters from ISO-8859-1 (Latin-1) encoding to UTF-8.
     *
     * EXAMPLE:
     * ```php
     * UTF8::utf8Decode(UTF8::utf8Encode('-ABC-中文空白-')); // '-ABC-中文空白-'
     * ```
     *
     * @param string $str The input ISO-8859-1 encoded string.
     *
     * @psalm-pure
     *
     * @return string The UTF-8 encoded string.
     */
    public static function utf8Encode(string $str): string
    {
        if ($str === '') {
            return '';
        }

        return mb_convert_encoding($str, self::UTF8, self::ISO88591);
    }

    /**
     * Returns an array with all utf8 whitespace characters.
     *
     * @see http://www.bogofilter.org/pipermail/bogofilter/2003-March/001889.html
     *
     * @psalm-pure
     *
     * @return string[]
     *                  An array with all known whitespace characters as values and the type of whitespace as keys
     *                  as defined in above URL
     */
    public static function whitespace_table(): array
    {
        return self::$WHITESPACE_TABLE;
    }

    /**
     * Limit the number of words in a string.
     *
     * EXAMPLE: <code>UTF8::words_limit('fòô bàř fòô', 2, ''); // 'fòô bàř'</code>
     *
     * @param string      $str        <p>The input string.</p>
     * @param int<1, max> $limit      <p>The limit of words as integer.</p>
     * @param string      $str_add_on <p>Replacement for the striped string.</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function words_limit(
        string $str,
        int $limit = 100,
        string $str_add_on = '…'
    ): string {
        if (
            $str === ''
            ||
            /* @phpstan-ignore-next-line | we do not trust the phpdoc check */
            $limit <= 0
        ) {
            return '';
        }

        \preg_match('/^\\s*+(?:[^\\s]++\\s*+){1,' . $limit . '}/u', $str, $matches);

        if (
            !isset($matches[0])
            ||
            \mb_strlen($str) === (int) \mb_strlen($matches[0])
        ) {
            return $str;
        }

        return \rtrim($matches[0]) . $str_add_on;
    }

    /**
     * Wraps a string to a given number of characters
     *
     * EXAMPLE: <code>UTF8::wordwrap('Iñtërnâtiônàlizætiøn', 2, '<br>', true)); // 'Iñ<br>të<br>rn<br>ât<br>iô<br>nà<br>li<br>zæ<br>ti<br>øn'</code>
     *
     * @see http://php.net/manual/en/function.wordwrap.php
     *
     * @param string      $str   <p>The input string.</p>
     * @param int<1, max> $width [optional] <p>The column width.</p>
     * @param string      $break [optional] <p>The line is broken using the optional break parameter.</p>
     * @param bool        $cut   [optional] <p>
     *                      If the cut is set to true, the string is
     *                      always wrapped at or before the specified width. So if you have
     *                      a word that is larger than the given width, it is broken apart.
     *                      </p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>The given string wrapped at the specified column.</p>
     */
    public static function wordwrap(
        string $str,
        int $width = 75,
        string $break = "\n",
        bool $cut = false
    ): string {
        if ($str === '' || $break === '') {
            return '';
        }

        $strSplit = \explode($break, $str);

        /** @var string[] $charsArray */
        $charsArray = [];
        $word_split = '';
        foreach ($strSplit as $i => $i_value) {
            if ($i) {
                $charsArray[] = $break;
                $word_split .= '#';
            }

            foreach (self::strSplit($i_value) as $c) {
                $charsArray[] = $c;
                if ($c === ' ') {
                    $word_split .= ' ';
                } else {
                    $word_split .= '?';
                }
            }
        }

        $str_return = '';
        $j = 0;
        $b = -1;
        $i = -1;
        $word_split = \wordwrap($word_split, $width, '#', $cut);

        $max = \mb_strlen($word_split);
        /** @noinspection PhpAssignmentInConditionInspection - is ok here */
        while (($b = \mb_strpos($word_split, '#', $b + 1)) !== false) {
            for (++$i; $i < $b; ++$i) {
                if (isset($charsArray[$j])) {
                    $str_return .= $charsArray[$j];
                    unset($charsArray[$j]);
                }
                ++$j;

                // prevent endless loop, e.g. if there is a error in the "mb_*" polyfill
                if ($i > $max) {
                    break 2;
                }
            }

            if (
                $break === $charsArray[$j]
                ||
                $charsArray[$j] === ' '
            ) {
                unset($charsArray[$j++]);
            }

            $str_return .= $break;

            // prevent endless loop, e.g. if there is a error in the "mb_*" polyfill
            if ($b > $max) {
                break;
            }
        }

        return $str_return . \implode('', $charsArray);
    }

    /**
     * Line-Wrap the string after $limit, but split the string by "$delimiter" before ...
     *    ... so that we wrap the per line.
     *
     * @param string      $str             <p>The input string.</p>
     * @param int<1, max> $width           [optional] <p>The column width.</p>
     * @param string      $break           [optional] <p>The line is broken using the optional break parameter.</p>
     * @param bool        $cut             [optional] <p>
     *                                     If the cut is set to true, the string is
     *                                     always wrapped at or before the specified width. So if you have
     *                                     a word that is larger than the given width, it is broken apart.
     *                                     </p>
     * @param bool        $add_final_break [optional] <p>
     *                                     If this flag is true, then the method will add a $break at the end
     *                                     of the result string.
     *                                     </p>
     * @param non-empty-string|null $delimiter       [optional] <p>
     *                                     You can change the default behavior, where we split the string by newline.
     *                                     </p>
     *
     * @psalm-pure
     *
     * @return string
     */
    public static function wordwrap_per_line(
        string $str,
        int $width = 75,
        string $break = "\n",
        bool $cut = false,
        bool $add_final_break = true,
        ?string $delimiter = null
    ): string {
        if ($delimiter === null) {
            $strings = \preg_split('/\\r\\n|\\r|\\n/', $str);
        } else {
            $strings = \explode($delimiter, $str);
        }

        $string_helper_array = [];
        if ($strings !== false) {
            foreach ($strings as $value) {
                $string_helper_array[] = self::wordwrap($value, $width, $break, $cut);
            }
        }

        if ($add_final_break) {
            $final_break = $break;
        } else {
            $final_break = '';
        }

        return \implode($delimiter ?? "\n", $string_helper_array) . $final_break;
    }

    /**
     * Returns an array of Unicode White Space characters.
     *
     * @psalm-pure
     *
     * @return string[]
     *                  <p>An array with numeric code point as key and White Space Character as value.</p>
     */
    public static function ws(): array
    {
        return self::$WHITESPACE;
    }

    /**
     * Checks whether the passed string contains only byte sequences that are valid UTF-8 characters.
     *
     * EXAMPLE: <code>
     * UTF8::isUtf8String('Iñtërnâtiônàlizætiøn'); // true
     * //
     * UTF8::isUtf8String("Iñtërnâtiônàlizætiøn\xA0\xA1"); // false
     * </code>
     *
     * @see          http://hsivonen.iki.fi/php-utf8/
     *
     * @param string $str    The string to be checked.
     * @param bool   $strict Check also if the string is not UTF-16 or UTF-32.
     *
     * @psalm-pure
     *
     * @return bool
     */
    private static function isUtf8String(string $str, bool $strict = false): bool
    {
        if ($str === '') {
            return true;
        }

        if ($strict) {
            $isBinary = self::isBinary($str, true);

            if ($isBinary && self::isUtf16($str, false) !== false) {
                return false;
            }

            if ($isBinary && self::isUtf32($str, false) !== false) {
                return false;
            }
        }

        if (self::$SUPPORT[self::FEATURE_PCREUTF8]) {
            // If even just the first character can be matched with the /u modifier, it's valid UTF-8
            return preg_match('/^./us', $str) === 1;
        }

        $mState = 0;  // Cached expected number of octets after the current octet
        $mUcs4 = 0;   // Cached Unicode character
        $mBytes = 1;  // Cached expected number of octets in the current sequence

        if (self::$ORD === null) {
            self::$ORD = self::getData('ord');
        }

        $len = strlen($str);
        for ($i = 0; $i < $len; ++$i) {
            $in = self::$ORD[$str[$i]];

            if ($mState === 0) {
                // Handle US-ASCII or multi-octet sequence
                if ((0x80 & $in) === 0) {
                    $mBytes = 1;  // US-ASCII character
                } elseif ((0xE0 & $in) === 0xC0) {
                    // First octet of 2-byte sequence
                    $mUcs4 = ($in & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } elseif ((0xF0 & $in) === 0xE0) {
                    // First octet of 3-byte sequence
                    $mUcs4 = ($in & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } elseif ((0xF8 & $in) === 0xF0) {
                    // First octet of 4-byte sequence
                    $mUcs4 = ($in & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } elseif ((0xFC & $in) === 0xF8) {
                    // First octet of 5-byte sequence (illegal)
                    $mUcs4 = ($in & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } elseif ((0xFE & $in) === 0xFC) {
                    // First octet of 6-byte sequence (illegal)
                    $mUcs4 = ($in & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    return false;  // Invalid first octet
                }
            } elseif ((0xC0 & $in) === 0x80) {
                // Continuation octet in multi-octet sequence
                $shift = ($mState - 1) * 6;
                $tmp = $in & 0x3F;
                $mUcs4 |= $tmp << $shift;

                if (--$mState === 0) {
                    // Check for illegal sequences and code points
                    if (($mBytes === 2 && $mUcs4 < 0x0080) ||
                        ($mBytes === 3 && $mUcs4 < 0x0800) ||
                        ($mBytes === 4 && $mUcs4 < 0x10000) ||
                        ($mBytes > 4) ||
                        (($mUcs4 & 0xFFFFF800) === 0xD800) || // Illegal surrogate pair
                        ($mUcs4 > 0x10FFFF)) {
                        return false;
                    }

                    // Reset UTF-8 cache
                    $mState = 0;
                    $mUcs4 = 0;
                    $mBytes = 1;
                }
            } else {
                return false;  // Incomplete or illegal sequence
            }
        }

        return $mState === 0;  // Valid UTF-8 if no incomplete sequence left
    }

    /**
     * @param string $str
     * @param bool   $use_lowercase      <p>Use uppercase by default, otherwise use lowercase.</p>
     * @param bool   $use_full_case_fold <p>Convert not only common cases.</p>
     *
     * @psalm-pure
     *
     * @return string
     */
    private static function fixStrCaseHelper(
        string $str,
        bool $use_lowercase = false,
        bool $use_full_case_fold = false
    ) {
        $upper = self::$COMMON_CASE_FOLD['upper'];
        $lower = self::$COMMON_CASE_FOLD['lower'];

        if ($use_lowercase) {
            $str = \str_replace(
                $upper,
                $lower,
                $str
            );
        } else {
            $str = \str_replace(
                $lower,
                $upper,
                $str
            );
        }

        if ($use_full_case_fold) {
            /**
             * @psalm-suppress ImpureStaticVariable
             *
             * @var array<mixed>|null
             */
            static $FULL_CASE_FOLD = null;
            if ($FULL_CASE_FOLD === null) {
                $FULL_CASE_FOLD = self::getData('caseFolding_full');
            }

            if ($use_lowercase) {
                $str = \str_replace($FULL_CASE_FOLD[0], $FULL_CASE_FOLD[1], $str);
            } else {
                $str = \str_replace($FULL_CASE_FOLD[1], $FULL_CASE_FOLD[0], $str);
            }
        }

        return $str;
    }

    /**
     * get data from "/data/*.php"
     *
     * @param string $file
     *
     * @psalm-pure
     *
     * @return array<array-key, mixed>
     */
    private static function getData(string $file)
    {
        /** @noinspection PhpIncludeInspection */
        /** @noinspection UsingInclusionReturnValueInspection */
        /** @psalm-suppress UnresolvableInclude */
        return include __DIR__ . '/data/' . $file . '.php';
    }

    /**
     * Initializes caches for emoji encoding.
     */
    private static function initEmojiEncodeData(): void
    {
        if (self::$EMOJI_ENCODE_KEYS_CACHE !== null) {
            return;
        }

        if (self::$EMOJI_DECODE_VALUES_CACHE === null) {
            self::initEmojiDecodeData();
        }

        self::$EMOJI_ENCODE_KEYS_CACHE = self::$EMOJI_DECODE_VALUES_CACHE;
        self::$EMOJI_ENCODE_VALUES_CACHE = self::$EMOJI_DECODE_KEYS_CACHE;
    }

    /**
     * Initializes caches for emoji decoding.
     */
    private static function initEmojiDecodeData(): void
    {
        if (self::$EMOJI_DECODE_KEYS_CACHE !== null) {
            return;
        }

        if (self::$EMOJI === null) {
            self::$EMOJI = self::getData('emoji');
        }

        // Sort by length for correct replacements
        uksort(self::$EMOJI, static fn(string $a, string $b): int => strlen($b) <=> strlen($a));

        self::$EMOJI_DECODE_KEYS_CACHE = array_keys(self::$EMOJI);
        self::$EMOJI_DECODE_VALUES_CACHE = self::$EMOJI;

        foreach (self::$EMOJI_DECODE_KEYS_CACHE as $key) {
            $hash = crc32($key);
            self::$EMOJI_KEYS_REVERSIBLE_CACHE[] = "_-_PORTABLE_UTF8_-_{$hash}_-_" . strrev((string) $hash) . "_-_8FTU_ELBATROP_-_";
        }
    }

    /**
     * @param string[] $strings
     * @param bool     $remove_empty_values
     * @param int|null $remove_short_values
     *
     * @psalm-pure
     *
     * @return list<string>
     */
    private static function reduce_string_array(
        array $strings,
        bool $remove_empty_values,
        ?int $remove_short_values = null
    ) {
        // init
        $return = [];

        foreach ($strings as &$str) {
            if (
                $remove_short_values !== null
                &&
                \mb_strlen($str) <= $remove_short_values
            ) {
                continue;
            }

            if (
                $remove_empty_values
                &&
                \trim($str) === ''
            ) {
                continue;
            }

            $return[] = $str;
        }

        return $return;
    }

    /**
     * rxClass
     *
     * @param string $s
     * @param string $class
     *
     * @return string
     *
     * @psalm-pure
     */
    private static function rxClass(string $s, string $class = '')
    {
        /**
         * @psalm-suppress ImpureStaticVariable
         *
         * @var array<string,string>
         */
        static $RX_CLASS_CACHE = [];

        $cache_key = $s . '_' . $class;

        if (isset($RX_CLASS_CACHE[$cache_key])) {
            return $RX_CLASS_CACHE[$cache_key];
        }

        $class_array = [];
        $class_array[] = $class;

        /** @noinspection SuspiciousLoopInspection */
        /** @noinspection AlterInForeachInspection */
        foreach (self::strSplit($s) as &$s) {
            if ($s === '-') {
                $class_array[0] = '-' . $class_array[0];
            } elseif (!isset($s[2])) {
                $class_array[0] .= \preg_quote($s, '/');
            } elseif (self::strlen($s) === 1) {
                $class_array[0] .= $s;
            } else {
                $class_array[] = $s;
            }
        }

        if ($class_array[0]) {
            $class_array[0] = '[' . $class_array[0] . ']';
        }

        if (\count($class_array) === 1) {
            $return = $class_array[0];
        } else {
            $return = '(?:' . \implode('|', $class_array) . ')';
        }

        $RX_CLASS_CACHE[$cache_key] = $return;

        return $return;
    }

    /**
     * Personal names such as "Marcus Aurelius" are sometimes typed incorrectly using lowercase ("marcus aurelius").
     *
     * @param string $names
     * @param string $delimiter
     * @param string $encoding
     *
     * @phpstan-param non-empty-string $delimiter
     *
     * @psalm-pure
     *
     * @return string
     */
    private static function str_capitalize_name_helper(
        string $names,
        string $delimiter,
        string $encoding = self::UTF8
    ) {
        // init
        try {
            $name_helper_array = \explode($delimiter, $names);
        } catch (\Error $e) {
            // PHP >= 8.0 : explode() will now throw ValueError when separator parameter is given an empty string (""). Previously, explode() returned false instead.
            $name_helper_array = false;
        }
        if ($name_helper_array === false) {
            return '';
        }

        $special_cases = [
            'names' => [
                'ab',
                'af',
                'al',
                'and',
                'ap',
                'bint',
                'binte',
                'da',
                'de',
                'del',
                'den',
                'der',
                'di',
                'dit',
                'ibn',
                'la',
                'mac',
                'nic',
                'of',
                'ter',
                'the',
                'und',
                'van',
                'von',
                'y',
                'zu',
            ],
            'prefixes' => [
                'al-',
                "d'",
                'ff',
                "l'",
                'mac',
                'mc',
                'nic',
            ],
        ];

        foreach ($name_helper_array as &$name) {
            if (\in_array($name, $special_cases['names'], true)) {
                continue;
            }

            $continue = false;

            if ($delimiter === '-') {
                foreach ((array) $special_cases['names'] as &$beginning) {
                    if (\strncmp($name, $beginning, \strlen($beginning)) === 0) {
                        $continue = true;

                        break;
                    }
                }
                unset($beginning);
            }

            foreach ((array) $special_cases['prefixes'] as &$beginning) {
                if (\strncmp($name, $beginning, \strlen($beginning)) === 0) {
                    $continue = true;

                    break;
                }
            }
            unset($beginning);

            if ($continue) {
                continue;
            }

            $name = self::ucfirst($name, $encoding);
        }

        return \implode($delimiter, $name_helper_array);
    }

    /**
     * Generic case-sensitive transformation for collation matching.
     *
     * @param string $str <p>The input string</p>
     *
     * @psalm-pure
     *
     * @return string|null
     */
    private static function strtonatfold(string $str)
    {
        $str = \Normalizer::normalize($str, \Normalizer::NFD);
        if ($str === false) {
            return '';
        }

        return \preg_replace(
            '/\p{Mn}+/u',
            '',
            $str
        );
    }

    /**
     * Converts a character from Windows-1252 encoding to UTF-8 if necessary.
     *
     * This function helps convert non-UTF-8 characters into their UTF-8 equivalent
     * while handling special Windows-1252 cases.
     *
     * @param int|string $input The character to convert.
     *
     * @psalm-pure
     *
     * @return string The UTF-8 encoded character.
     */
    private static function toUtf8ConvertHelper($input): string
    {
        if (self::$ORD === null) {
            self::$ORD = self::getData('ord');
        }

        if (self::$CHR === null) {
            self::$CHR = self::getData('chr');
        }

        if (self::$WIN1252_TO_UTF8 === null) {
            self::$WIN1252_TO_UTF8 = self::getData('win1252_toUtf8');
        }

        $ordC1 = self::$ORD[$input] ?? null;

        // Check if character has a direct Windows-1252 to UTF-8 mapping
        if ($ordC1 !== null && isset(self::$WIN1252_TO_UTF8[$ordC1])) {
            return self::$WIN1252_TO_UTF8[$ordC1];
        }

        // Convert character using UTF-8 encoding rules
        return (self::$CHR[intdiv($ordC1, 64)] | "\xC0") . (($input & "\x3F") | "\x80");
    }

/**
     * Decodes Unicode-encoded URL segments.
     *
     * Converts `%uXXXX` sequences into their corresponding HTML entity (`&#xXXXX;`).
     *
     * @param string $str The input string potentially containing Unicode-encoded URL segments.
     *
     * @psalm-pure
     *
     * @return string The decoded string.
     */
    private static function urlDecodeUnicodeHelper(string $str): string
    {
        if (strpos($str, '%u') === false) {
            return $str;
        }

        return preg_replace('/%u([0-9a-fA-F]{3,4})/', '&#x\1;', $str) ?? $str;
    }

    /**
     * Polyfill for FILTER_SANITIZE_STRING (deprecated in PHP 8.1+).
     *
     * Removes null bytes and HTML tags while replacing quotes with HTML entities.
     *
     * @see https://stackoverflow.com/a/69207369/1155858
     *
     * @param string $str The input string to be sanitized.
     *
     * @return false|string The sanitized string, or false on failure.
     */
    private static function filterSanitizeStringPolyfill(string $str)
    {
        $cleanedStr = preg_replace('/\x00|<[^>]*>?/', '', $str);

        return $cleanedStr !== null ? str_replace(["'", '"'], ['&#39;', '&#34;'], $cleanedStr) : false;
    }


    /**
     * Helper function to check if an extension is loaded.
     *
     * @psalm-pure
     *
     * @param string $extension The name of the extension.
     * @return bool True if the extension is loaded, false otherwise.
     */
    private static function isExtensionLoaded(string $extension): bool
    {
        return extension_loaded($extension);
    }

    /**
     * Helper function to check if a function exists.
     *
     * @psalm-pure
     *
     * @param string $function The name of the function.
     * @return bool True if the function exists, false otherwise.
     */
    private static function isFunctionExists(string $function): bool
    {
        return function_exists($function);
    }

    /**
     * Helper function to check if a class exists.
     *
     * @psalm-pure
     *
     * @param string $class The name of the class.
     * @return bool True if the class exists, false otherwise.
     */
    private static function isClassExists(string $class): bool
    {
        return class_exists($class);
    }
}
