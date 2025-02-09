<?php

declare(strict_types=1);

namespace jessegreathouse\helper;

final class ASCII
{
    //
    // INFO: https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes
    //

    const UZBEK_LANGUAGE_CODE = 'uz';

    const TURKMEN_LANGUAGE_CODE = 'tk';

    const THAI_LANGUAGE_CODE = 'th';

    const PASHTO_LANGUAGE_CODE = 'ps';

    const ORIYA_LANGUAGE_CODE = 'or';

    const MONGOLIAN_LANGUAGE_CODE = 'mn';

    const KOREAN_LANGUAGE_CODE = 'ko';

    const KIRGHIZ_LANGUAGE_CODE = 'ky';

    const ARMENIAN_LANGUAGE_CODE = 'hy';

    const BENGALI_LANGUAGE_CODE = 'bn';

    const BELARUSIAN_LANGUAGE_CODE = 'be';

    const AMHARIC_LANGUAGE_CODE = 'am';

    const JAPANESE_LANGUAGE_CODE = 'ja';

    const CHINESE_LANGUAGE_CODE = 'zh';

    const DUTCH_LANGUAGE_CODE = 'nl';

    const ITALIAN_LANGUAGE_CODE = 'it';

    const MACEDONIAN_LANGUAGE_CODE = 'mk';

    const PORTUGUESE_LANGUAGE_CODE = 'pt';

    const GREEKLISH_LANGUAGE_CODE = 'el__greeklish';

    const GREEK_LANGUAGE_CODE = 'el';

    const HINDI_LANGUAGE_CODE = 'hi';

    const SWEDISH_LANGUAGE_CODE = 'sv';

    const TURKISH_LANGUAGE_CODE = 'tr';

    const BULGARIAN_LANGUAGE_CODE = 'bg';

    const HUNGARIAN_LANGUAGE_CODE = 'hu';

    const MYANMAR_LANGUAGE_CODE = 'my';

    const CROATIAN_LANGUAGE_CODE = 'hr';

    const FINNISH_LANGUAGE_CODE = 'fi';

    const GEORGIAN_LANGUAGE_CODE = 'ka';

    const RUSSIAN_LANGUAGE_CODE = 'ru';

    const RUSSIAN_PASSPORT_2013_LANGUAGE_CODE = 'ru__passport_2013';

    const RUSSIAN_GOST_2000_B_LANGUAGE_CODE = 'ru__gost_2000_b';

    const UKRAINIAN_LANGUAGE_CODE = 'uk';

    const KAZAKH_LANGUAGE_CODE = 'kk';

    const CZECH_LANGUAGE_CODE = 'cs';

    const DANISH_LANGUAGE_CODE = 'da';

    const POLISH_LANGUAGE_CODE = 'pl';

    const ROMANIAN_LANGUAGE_CODE = 'ro';

    const ESPERANTO_LANGUAGE_CODE = 'eo';

    const ESTONIAN_LANGUAGE_CODE = 'et';

    const LATVIAN_LANGUAGE_CODE = 'lv';

    const LITHUANIAN_LANGUAGE_CODE = 'lt';

    const NORWEGIAN_LANGUAGE_CODE = 'no';

    const VIETNAMESE_LANGUAGE_CODE = 'vi';

    const ARABIC_LANGUAGE_CODE = 'ar';

    const PERSIAN_LANGUAGE_CODE = 'fa';

    const SERBIAN_LANGUAGE_CODE = 'sr';

    const SERBIAN_CYRILLIC_LANGUAGE_CODE = 'sr__cyr';

    const SERBIAN_LATIN_LANGUAGE_CODE = 'sr__lat';

    const AZERBAIJANI_LANGUAGE_CODE = 'az';

    const SLOVAK_LANGUAGE_CODE = 'sk';

    const FRENCH_LANGUAGE_CODE = 'fr';

    const FRENCH_AUSTRIAN_LANGUAGE_CODE = 'fr_at';

    const FRENCH_SWITZERLAND_LANGUAGE_CODE = 'fr_ch';

    const GERMAN_LANGUAGE_CODE = 'de';

    const GERMAN_AUSTRIAN_LANGUAGE_CODE = 'de_at';

    const GERMAN_SWITZERLAND_LANGUAGE_CODE = 'de_ch';

    const ENGLISH_LANGUAGE_CODE = 'en';

    const EXTRA_LATIN_CHARS_LANGUAGE_CODE = 'latin';

    const EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE = ' ';

    const EXTRA_MSWORD_CHARS_LANGUAGE_CODE = 'msword';

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_MAPS;

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_MAPS_AND_EXTRAS;

    /**
     * @var array<string, array<string, string>>|null
     */
    private static $ASCII_EXTRAS;

    /**
     * @var array<string, int>|null
     */
    private static $ORD;

    /**
     * @var array<string, int>|null
     */
    private static $LANGUAGE_MAX_KEY;

    /**
     * url: https://en.wikipedia.org/wiki/Wikipedia:ASCII#ASCII_printable_characters
     *
     * @var string
     */
    private static $REGEX_ASCII = "[^\x09\x10\x13\x0A\x0D\x20-\x7E]";

    /**
     * bidirectional text chars
     *
     * url: https://www.w3.org/International/questions/qa-bidi-unicode-controls
     *
     * @var array<int, string>
     */
    private static $BIDI_UNI_CODE_CONTROLS_TABLE = [
        // LEFT-TO-RIGHT EMBEDDING (use -> dir = "ltr")
        8234 => "\xE2\x80\xAA",
        // RIGHT-TO-LEFT EMBEDDING (use -> dir = "rtl")
        8235 => "\xE2\x80\xAB",
        // POP DIRECTIONAL FORMATTING // (use -> </bdo>)
        8236 => "\xE2\x80\xAC",
        // LEFT-TO-RIGHT OVERRIDE // (use -> <bdo dir = "ltr">)
        8237 => "\xE2\x80\xAD",
        // RIGHT-TO-LEFT OVERRIDE // (use -> <bdo dir = "rtl">)
        8238 => "\xE2\x80\xAE",
        // LEFT-TO-RIGHT ISOLATE // (use -> dir = "ltr")
        8294 => "\xE2\x81\xA6",
        // RIGHT-TO-LEFT ISOLATE // (use -> dir = "rtl")
        8295 => "\xE2\x81\xA7",
        // FIRST STRONG ISOLATE // (use -> dir = "auto")
        8296 => "\xE2\x81\xA8",
        // POP DIRECTIONAL ISOLATE
        8297 => "\xE2\x81\xA9",
    ];

    /**
     * Get all languages from the constants "ASCII::.*LANGUAGE_CODE".
     *
     * @return array<string, string>
     *                                 <p>An associative array where the key is the language code in lowercase
     *                                 and the value is the corresponding language string.</p>
     */
    public static function getAllLanguages(): array
    {
        // init
        static $LANGUAGES = [];

        if ($LANGUAGES !== []) {
            return $LANGUAGES;
        }

        foreach ((new \ReflectionClass(__CLASS__))->getConstants() as $constant => $lang) {
            if (\strpos($constant, 'EXTRA') !== false) {
                $LANGUAGES[\strtolower($constant)] = $lang;
            } else {
                $LANGUAGES[\strtolower(\str_replace('_LANGUAGE_CODE', '', $constant))] = $lang;
            }
        }

        return $LANGUAGES;
    }

    /**
     * Returns an replacement array for ASCII methods.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArray();
     * var_dump($array['ru']['б']); // 'b'
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     *
     * @psalm-pure
     *
     * @return array<string, array<string , string>>
     *                                               <p>An array where the key is the language code, and the value is
     *                                               an associative array mapping original characters to their replacements.</p>
     */
    public static function charsArray(bool $replace_extra_symbols = false): array
    {
        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            return self::$ASCII_MAPS_AND_EXTRAS ?? [];
        }

        self::prepareAsciiMaps();

        return self::$ASCII_MAPS ?? [];
    }

    /**
     * Returns an replacement array for ASCII methods with a mix of multiple languages.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithMultiLanguageValues();
     * var_dump($array['b']); // ['β', 'б', 'ဗ', 'ბ', 'ب']
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     *
     * @psalm-pure
     *
     * @return array<string, list<string>>
     *                                     <p>An array of replacements.</p>
     */
    public static function charsArrayWithMultiLanguageValues(bool $replace_extra_symbols = false): array
    {
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols;

        if (isset($CHARS_ARRAY[$cacheKey])) {
            return $CHARS_ARRAY[$cacheKey];
        }

        // init
        $return = [];
        $language_all_chars = self::charsArrayWithSingleLanguageValues(
            $replace_extra_symbols,
            false
        );

        /* @noinspection AlterInForeachInspection | ok here */
        foreach ($language_all_chars as $key => &$value) {
            $return[$value][] = $key;
        }

        $CHARS_ARRAY[$cacheKey] = $return;

        return $return;
    }

    /**
     * Returns an replacement array for ASCII methods with one language.
     *
     * For example, German will map 'ä' to 'ae', while other languages
     * will simply return e.g. 'a'.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithOneLanguage('ru');
     * $tmpKey = \array_search('yo', $array['replace']);
     * echo $array['orig'][$tmpKey]; // 'ё'
     * </code>
     *
     * @param string $language              [optional] <p>Language of the source string e.g.: en, de_at, or de-ch.
     *                                      (default is 'en') | ASCII::*_LANGUAGE_CODE</p>
     * @param bool   $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     * @param bool   $asOrigReplaceArray    [optional] <p>TRUE === return {orig: list<string>, replace: list<string>}
     *                                      array</p>
     *
     * @psalm-pure
     *
     * @return ($asOrigReplaceArray is true ? array{orig: list<string>, replace: list<string>} : array<string, string>)
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function charsArrayWithOneLanguage(
        string $language = self::ENGLISH_LANGUAGE_CODE,
        bool $replace_extra_symbols = false,
        bool $asOrigReplaceArray = true
    ): array {
        $language = self::getLanguage($language);

        // init
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;

        // check static cache
        if (isset($CHARS_ARRAY[$cacheKey][$language])) {
            return $CHARS_ARRAY[$cacheKey][$language];
        }

        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            if (isset(self::$ASCII_MAPS_AND_EXTRAS[$language])) {
                $tmpArray = self::$ASCII_MAPS_AND_EXTRAS[$language];

                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => \array_keys($tmpArray),
                        'replace' => \array_values($tmpArray),
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
                }
            } else {
                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => [],
                        'replace' => [],
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = [];
                }
            }
        } else {
            self::prepareAsciiMaps();

            if (isset(self::$ASCII_MAPS[$language])) {
                $tmpArray = self::$ASCII_MAPS[$language];

                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => \array_keys($tmpArray),
                        'replace' => \array_values($tmpArray),
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = $tmpArray;
                }
            } else {
                if ($asOrigReplaceArray) {
                    $CHARS_ARRAY[$cacheKey][$language] = [
                        'orig'    => [],
                        'replace' => [],
                    ];
                } else {
                    $CHARS_ARRAY[$cacheKey][$language] = [];
                }
            }
        }

        return $CHARS_ARRAY[$cacheKey][$language] ?? ['orig' => [], 'replace' => []];
    }

    /**
     * Returns an replacement array for ASCII methods with multiple languages.
     *
     * EXAMPLE: <code>
     * $array = ASCII::charsArrayWithSingleLanguageValues();
     * $tmpKey = \array_search('hnaik', $array['replace']);
     * echo $array['orig'][$tmpKey]; // '၌'
     * </code>
     *
     * @param bool $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with " pound ".</p>
     * @param bool $asOrigReplaceArray    [optional] <p>TRUE === return {orig: list<string>, replace: list<string>}
     *                                    array</p>
     *
     * @psalm-pure
     *
     * @return ($asOrigReplaceArray is true ? array{orig: list<string>, replace: list<string>} : array<string, string>)
     */
    public static function charsArrayWithSingleLanguageValues(
        bool $replace_extra_symbols = false,
        bool $asOrigReplaceArray = true
    ): array {
        // init
        static $CHARS_ARRAY = [];
        $cacheKey = '' . $replace_extra_symbols . '-' . $asOrigReplaceArray;

        if (isset($CHARS_ARRAY[$cacheKey])) {
            return $CHARS_ARRAY[$cacheKey];
        }

        if ($replace_extra_symbols) {
            self::prepareAsciiAndExtrasMaps();

            /* @noinspection AlterInForeachInspection | ok here */
            foreach (self::$ASCII_MAPS_AND_EXTRAS ?? [] as &$map) {
                $CHARS_ARRAY[$cacheKey][] = $map;
            }
        } else {
            self::prepareAsciiMaps();

            /* @noinspection AlterInForeachInspection | ok here */
            foreach (self::$ASCII_MAPS ?? [] as &$map) {
                $CHARS_ARRAY[$cacheKey][] = $map;
            }
        }

        $CHARS_ARRAY[$cacheKey] = \array_merge([], ...$CHARS_ARRAY[$cacheKey]);

        if ($asOrigReplaceArray) {
            $CHARS_ARRAY[$cacheKey] = [
                'orig'    => \array_keys($CHARS_ARRAY[$cacheKey]),
                'replace' => \array_values($CHARS_ARRAY[$cacheKey]),
            ];
        }

        return $CHARS_ARRAY[$cacheKey];
    }

    /**
     * Accepts a string and removes all non-UTF-8 characters from it + extras if needed.
     *
     * @param string $str                         <p>The string to be sanitized.</p>
     * @param bool   $normalizeWhitespace        [optional] <p>Set to true, if you need to normalize the
     *                                            whitespace.</p>
     * @param bool   $normalizeMsWord            [optional] <p>Set to true, if you need to normalize MS Word chars
     *                                            e.g.: "…"
     *                                            => "..."</p>
     * @param bool   $keep_non_breaking_space     [optional] <p>Set to true, to keep non-breaking-spaces, in
     *                                            combination with
     *                                            $normalizeWhitespace</p>
     * @param bool   $removeInvisibleCharacters [optional] <p>Set to false, if you not want to remove invisible
     *                                            characters e.g.: "\0"</p>
     *
     * @psalm-pure
     *
     * @return string
     *                <p>A clean UTF-8 string.</p>
     */
    public static function clean(
        string $str,
        bool $normalizeWhitespace = true,
        bool $keep_non_breaking_space = false,
        bool $normalizeMsWord = true,
        bool $removeInvisibleCharacters = true
    ): string {
        // http://stackoverflow.com/questions/1401317/remove-non-utf8-characters-from-string
        // caused connection reset problem on larger strings

        $regex = '/
          (
            (?: [\x00-\x7F]               # single-byte sequences   0xxxxxxx
            |   [\xC0-\xDF][\x80-\xBF]    # double-byte sequences   110xxxxx 10xxxxxx
            |   [\xE0-\xEF][\x80-\xBF]{2} # triple-byte sequences   1110xxxx 10xxxxxx * 2
            |   [\xF0-\xF7][\x80-\xBF]{3} # quadruple-byte sequence 11110xxx 10xxxxxx * 3
            ){1,100}                      # ...one or more times
          )
        | ( [\x80-\xBF] )                 # invalid byte in range 10000000 - 10111111
        | ( [\xC0-\xFF] )                 # invalid byte in range 11000000 - 11111111
        /x';
        $str = (string) \preg_replace($regex, '$1', $str);

        if ($normalizeWhitespace) {
            $str = self::normalizeWhitespace($str, $keep_non_breaking_space);
        }

        if ($normalizeMsWord) {
            $str = self::normalizeMsWord($str);
        }

        if ($removeInvisibleCharacters) {
            $str = self::removeInvisibleCharacters($str);
        }

        return $str;
    }

    /**
     * Checks if a string is 7-bit ASCII.
     *
     * EXAMPLE: <code>
     * ASCII::isAscii('白'); // false
     * </code>
     *
     * @param string $str The string to check.
     *
     * @psalm-pure
     *
     * @return bool 
     *         true if the string is ASCII, false otherwise.
     */
    public static function isAscii(string $str): bool {
        return $str === '' || !preg_match('/' . self::$REGEX_ASCII . '/', $str);
    }

    /**
     * Returns a string with smart quotes, ellipsis characters, and dashes from
     * Windows-1252 (commonly used in Word documents) replaced by their ASCII
     * equivalents.
     *
     * EXAMPLE: <code>
     * ASCII::normalizeMsWord('„Abcdef…”'); // '"Abcdef..."'
     * </code>
     *
     * @param string $str The string to be normalized.
     *
     * @psalm-pure
     *
     * @return string A string with normalized characters for commonly used chars in Word documents.
     */
    public static function normalizeMsWord(string $str): string
    {
        if ($str === '') {
            return '';
        }

        static $msWordCache = ['orig' => [], 'replace' => []];

        if (empty($msWordCache['orig'])) {
            self::prepareAsciiMaps();

            $map = self::$ASCII_MAPS[self::EXTRA_MSWORD_CHARS_LANGUAGE_CODE] ?? [];

            $msWordCache = [
                'orig'    => array_keys($map),
                'replace' => array_values($map),
            ];
        }

        return str_replace($msWordCache['orig'], $msWordCache['replace'], $str);
    }


    /**
     * Normalizes the whitespace in a string by converting control characters and handling non-breaking spaces.
     *
     * @param string $str The input string to normalize.
     * @param bool $keepNonBreakingSpace Whether to keep non-breaking spaces (default: false).
     * @param bool $keepBidiUnicodeControls Whether to keep bidirectional Unicode controls (default: false).
     * @param bool $normalizeControlCharacters Whether to convert control characters (default: false).
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
        if ($str === '') {
            return '';
        }

        static $whitespaceCache = [];
        $cacheKey = (int) $keepNonBreakingSpace;

        // Normalize control characters if requested
        if ($normalizeControlCharacters) {
            $str = str_replace(
                [
                    "\x0d\x0c",     // 'END OF LINE'
                    "\xe2\x80\xa8", // 'LINE SEPARATOR'
                    "\xe2\x80\xa9", // 'PARAGRAPH SEPARATOR'
                    "\x0c",         // 'FORM FEED' // "\f"
                    "\x0b",         // 'VERTICAL TAB' // "\v"
                ],
                [
                    "\n",
                    "\n",
                    "\n",
                    "\n",
                    "\t",
                ],
                $str
            );
        }

        // Retrieve or build whitespace map cache
        if (!isset($whitespaceCache[$cacheKey])) {
            self::prepareAsciiMaps();

            // Cache the whitespace characters based on whether non-breaking space is kept
            $whitespaceCache[$cacheKey] = self::$ASCII_MAPS[self::EXTRA_WHITESPACE_CHARS_LANGUAGE_CODE] ?? [];

            // If non-breaking space is to be kept, remove it from the cache
            if ($keepNonBreakingSpace) {
                unset($whitespaceCache[$cacheKey]["\xc2\xa0"]);
            }

            // Convert cached characters to an array of their keys (characters)
            $whitespaceCache[$cacheKey] = array_keys($whitespaceCache[$cacheKey]);
        }

        // Remove bidirectional Unicode controls if necessary
        if (!$keepBidiUnicodeControls) {
            static $bidiUnicodeControlsCache = null;

            if ($bidiUnicodeControlsCache === null) {
                $bidiUnicodeControlsCache = self::$BIDI_UNI_CODE_CONTROLS_TABLE;
            }

            $str = str_replace($bidiUnicodeControlsCache, '', $str);
        }

        // Replace whitespace characters with spaces
        return str_replace($whitespaceCache[$cacheKey], ' ', $str);
    }

    /**
     * Removes invisible characters from a string to prevent malicious code injection.
     *
     * @param string $str The input string.
     * @param bool $urlEncoded Whether to remove URL-encoded control characters (default: false).
     *                         WARNING: May cause false positives (e.g., 'aa%0Baa' → 'aaaa').
     * @param string $replacement The character used for replacement (default: '').
     * @param bool $keepBasicControlCharacters Whether to keep basic control characters like [LRM] or [LSEP] (default: true).
     *
     * @psalm-pure
     *
     * @return string A sanitized string without invisible characters.
     */
    public static function removeInvisibleCharacters(
        string $str,
        bool $urlEncoded = false,
        string $replacement = '',
        bool $keepBasicControlCharacters = true
    ): string {
        $patterns = [];

        // Remove URL-encoded control characters if enabled
        if ($urlEncoded) {
            $patterns[] = '/%0[0-8bcefBCEF]/'; // URL-encoded 00-08, 11, 12, 14, 15
            $patterns[] = '/%1[0-9a-fA-F]/';   // URL-encoded 16-31
        }

        // Remove ASCII control characters (except basic ones if enabled)
        if ($keepBasicControlCharacters) {
            $patterns[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // ASCII 00-08, 11, 12, 14-31, 127
        } else {
            $str = self::normalizeWhitespace($str, false, false, true);
            $patterns[] = '/[^\P{C}\s]/u'; // Unicode control characters
        }

        // Iteratively remove matching patterns
        do {
            $str = preg_replace($patterns, $replacement, $str, -1, $count);
        } while ($count > 0);

        return $str;
    }

    /**
     * WARNING: This method will return broken characters and is only for special cases.
     *
     * Convert two UTF-8 encoded strings to single-byte strings suitable for
     * functions that need the same string length after the conversion.
     *
     * The function simply uses (and updates) a tailored dynamic encoding
     * (in/out map parameter) where non-ASCII characters are remapped to
     * the range [128-255] in order of appearance.
     *
     * @return array{0: string, 1: string}
     */
    public static function toAsciiRemap(string $str1, string $str2): array
    {
        $charMap = [];
        $str1 = self::toAsciiRemapIntern($str1, $charMap);
        $str2 = self::toAsciiRemapIntern($str2, $charMap);

        return [$str1, $str2];
    }

    /**
     * Returns an ASCII version of the string. A set of non-ASCII characters are
     * replaced with their closest ASCII counterparts, and the rest are removed
     * by default. The language or locale of the source string can be supplied
     * for language-specific transliteration in any of the following formats:
     * en, en_GB, or en-GB. For example, passing "de" results in "äöü" mapping
     * to "aeoeue" rather than "aou" as in other languages.
     *
     * EXAMPLE: <code>
     * ASCII::toAscii('�Düsseldorf�', 'en'); // Dusseldorf
     * </code>
     *
     * @param string $str                      The input string.
     * @param string $language                 [optional] Language of the source string. (default is 'en') | ASCII::*_LANGUAGE_CODE
     * @param bool   $removeUnsupportedChars   [optional] Whether to remove unsupported characters.
     * @param bool   $replaceExtraSymbols      [optional] Add some more replacements e.g. "£" with " pound".
     * @param bool   $useTransliterate         [optional] Use ASCII::toTransliterate() for unknown chars.
     * @param bool   $replaceSingleCharsOnly   [optional] Single char replacement is better for performance, but some languages need to replace more than one char.
     *
     * @psalm-pure
     *
     * @return string A string that contains only ASCII characters.
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function toAscii(
        string $str,
        string $language = self::ENGLISH_LANGUAGE_CODE,
        bool $removeUnsupportedChars = true,
        bool $replaceExtraSymbols = false,
        bool $useTransliterate = false,
        bool $replaceSingleCharsOnly = false
    ): string {
        if ($str === '') {
            return '';
        }

        // Resolve language
        $language = self::getLanguage($language);

        static $extraSymbolsCache = null;
        static $replaceHelperCache = [];
        $cacheKey = $language . '-' . $replaceExtraSymbols;

        if (!isset($replaceHelperCache[$cacheKey])) {
            $langAll = self::charsArrayWithSingleLanguageValues($replaceExtraSymbols, false);
            $langSpecific = self::charsArrayWithOneLanguage($language, $replaceExtraSymbols, false);

            $replaceHelperCache[$cacheKey] = $langSpecific ? array_merge([], $langAll, $langSpecific) : $langAll;
        }

        if ($replaceExtraSymbols && $extraSymbolsCache === null) {
            $extraSymbolsCache = array_reduce(self::$ASCII_EXTRAS ?? [], function ($cache, $extrasData) {
                return $cache . implode('', array_keys($extrasData));
            }, '');
        }

        $charDone = [];
        if (preg_match_all('/' . self::$REGEX_ASCII . ($replaceExtraSymbols ? '|[' . $extraSymbolsCache . ']' : '') . '/u', $str, $matches)) {
            if (!$replaceSingleCharsOnly) {
                $maxKeyLength = self::$LANGUAGE_MAX_KEY[$language] ?? 0;

                foreach ([5, 4, 3, 2] as $length) {
                    foreach ($matches[0] as $keyTmp => $char) {
                        $chars = implode('', array_slice($matches[0], $keyTmp, $length));
                        if ($chars && !isset($charDone[$chars]) && isset($replaceHelperCache[$cacheKey][$chars]) && strpos($str, $chars) !== false) {
                            $charDone[$chars] = true;
                            $str = str_replace($chars, $replaceHelperCache[$cacheKey][$chars], $str);
                        }
                    }
                }
            }

            foreach ($matches[0] as $char) {
                if (!isset($charDone[$char]) && isset($replaceHelperCache[$cacheKey][$char]) && strpos($str, $char) !== false) {
                    $charDone[$char] = true;
                    $str = str_replace($char, $replaceHelperCache[$cacheKey][$char], $str);
                }
            }
        }

        // Apply transliteration if necessary
        if (!isset(self::$ASCII_MAPS[$language])) {
            $useTransliterate = true;
        }

        if ($useTransliterate) {
            $str = self::toTransliterate($str, null, false);
        }

        // Remove unsupported characters
        if ($removeUnsupportedChars) {
            $str = str_replace(["\n\r", "\n", "\r", "\t"], ' ', $str);
            $str = preg_replace('/' . self::$REGEX_ASCII . '/', '', $str);
        }

        return $str;
    }

    /**
     * Convert the given string to a safe filename while preserving the string case.
     *
     * EXAMPLE: <code>
     * ASCII::toFilename('שדגשדג.png', true); // 'shdgshdg.png'
     * </code>
     *
     * @param string $str The input string.
     * @param bool $useTransliterate If true, transliteration is applied to unsafe characters.
     * @param string $fallbackChar The character to replace unsafe characters. Default is '-'.
     *
     * @return string A string that contains only safe characters for a filename.
     */
    public static function toFilename(
        string $str,
        bool $useTransliterate = true,
        string $fallbackChar = '-'
    ): string {
        if ($useTransliterate) {
            $str = self::toTransliterate($str, $fallbackChar);
        }

        $escapedFallbackChar = preg_quote($fallbackChar, '/');

        // Use preg_replace only once to handle all replacements
        $str = (string) preg_replace(
            [
                '/[^' . $escapedFallbackChar . '.\\-a-zA-Z\d\\s]/', // Remove unneeded chars
                '/\s+/u',                                            // Convert spaces to fallbackChar
                '/[' . $escapedFallbackChar . ']+/u',                // Remove consecutive fallbackChars
            ],
            [
                '',
                $fallbackChar,
                $fallbackChar,
            ],
            $str
        );

        return trim($str, $fallbackChar);
    }

    /**
     * Converts a string into a URL-friendly slug.
     *
     * - This includes replacing non-ASCII characters with their closest ASCII equivalents, removing remaining
     *   non-ASCII and non-alphanumeric characters, and replacing whitespace with $separator.
     * - The separator defaults to a single dash, and the string is also converted to lowercase.
     * - The language of the source string can also be supplied for language-specific transliteration.
     *
     * @param string                $str                   <p>The string input.</p>
     * @param string                $separator             [optional] <p>The string used to replace whitespace.</p>
     * @param string                $language              [optional] <p>Language of the source string.
     *                                                     (default is 'en') | ASCII::*_LANGUAGE_CODE</p>
     * @param array<string, string> $replacements          [optional] <p>A map of replaceable strings.</p>
     * @param bool                  $replace_extra_symbols [optional] <p>Add some more replacements e.g. "£" with "
     *                                                     pound ".</p>
     * @param bool                  $use_str_to_lower      [optional] <p>Use "string to lower" for the input.</p>
     * @param bool                  $use_transliterate     [optional] <p>Use ASCII::toTransliterate() for unknown
     *                                                     chars.</p>
     * @psalm-pure
     *
     * @return string
     *                <p>The URL-friendly slug.</p>
     *
     * @phpstan-param ASCII::*_LANGUAGE_CODE $language
     */
    public static function to_slugify(
        string $str,
        string $separator = '-',
        string $language = self::ENGLISH_LANGUAGE_CODE,
        array $replacements = [],
        bool $replace_extra_symbols = false,
        bool $use_str_to_lower = true,
        bool $use_transliterate = false
    ): string {
        if ($str === '') {
            return '';
        }

        foreach ($replacements as $from => $to) {
            $str = \str_replace($from, $to, $str);
        }

        $str = self::toAscii(
            $str,
            $language,
            false,
            $replace_extra_symbols,
            $use_transliterate
        );

        $str = \str_replace('@', $separator, $str);

        $str = (string) \preg_replace(
            '/[^a-zA-Z\\d\\s\\-_' . \preg_quote($separator, '/') . ']/',
            '',
            $str
        );

        if ($use_str_to_lower) {
            $str = \strtolower($str);
        }

        $str = (string) \preg_replace('/^[\'\\s]+|[\'\\s]+$/', '', $str);
        $str = (string) \preg_replace('/\\B([A-Z])/', '-\1', $str);
        $str = (string) \preg_replace('/[\\-_\\s]+/', $separator, $str);

        $l = \strlen($separator);
        if ($l && \strpos($str, $separator) === 0) {
            $str = (string) \substr($str, $l);
        }

        if (\substr($str, -$l) === $separator) {
            $str = (string) \substr($str, 0, \strlen($str) - $l);
        }

        return $str;
    }

    /**
     * Returns an ASCII version of the string. A set of non-ASCII characters are
     * replaced with their closest ASCII counterparts, and the rest are removed
     * unless instructed otherwise.
     *
     * EXAMPLE: <code>
     * ASCII::toTransliterate('déjà σσς iıii'); // 'deja sss iiii'
     * </code>
     *
     * @param string      $str     The input string.
     * @param string|null $unknown [optional] Character use if character is unknown. Default is '?'.
     *                              Use NULL to keep the unknown chars.
     * @param bool        $strict  [optional] Use "transliterator_transliterate()" from PHP-Intl.
     *
     * @psalm-pure
     *
     * @return string A String that contains only ASCII characters.
     */
    public static function toTransliterate(
        string $str,
        $unknown = '?',
        bool $strict = false
    ): string {
        static $utf8ToTranslit = null;
        static $transliterator = null;
        static $supportIntl = null;

        if ($str === '') {
            return '';
        }

        // Check if the string is already ASCII, for better performance
        if (self::isAscii($str)) {
            return $str;
        }

        // Check for the intl extension and prepare transliterator if necessary
        if ($supportIntl === null) {
            $supportIntl = extension_loaded('intl');
        }

        // Clean string for further processing
        $str = self::clean($str);

        // Check again if the string is ASCII after cleaning
        if (self::isAscii($str)) {
            return $str;
        }

        // If strict transliteration is enabled and intl extension is available, use transliterator
        if ($strict && $supportIntl) {
            if ($transliterator === null) {
                // Transliterator creation with predefined rules
                $transliterator = transliterator_create('NFKC; [:Nonspacing Mark:] Remove; NFKC; Any-Latin; Latin-ASCII;');
            }

            $strTmp = transliterator_transliterate($transliterator, $str);
            if ($strTmp !== false && self::isAscii($strTmp)) {
                return $strTmp;
            }

            $str = $strTmp ?? $str;
        }

        if (self::$ORD === null) {
            self::$ORD = self::getData('ascii_ord');
        }

        preg_match_all('/.|[^\x00]$/us', $str, $arrayTmp);
        $chars = $arrayTmp[0];
        $strTmp = '';
        
        foreach ($chars as $c) {
            $ordC0 = self::$ORD[$c[0]] ?? null;

            if ($ordC0 >= 0 && $ordC0 <= 127) {
                $strTmp .= $c;
                continue;
            }

            // Handle multi-byte UTF-8 characters
            $ord = self::calculateUtf8Order($c);

            if ($ord === null || $ordC0 === 254 || $ordC0 === 255) {
                $strTmp .= $unknown ?? $c;
                continue;
            }

            // Get transliteration mapping
            $bank = $ord >> 8;
            if (!isset($utf8ToTranslit[$bank])) {
                $utf8ToTranslit[$bank] = self::getDataIfExists(sprintf('x%03x', $bank));
            }

            $newChar = $ord & 255;
            $newChar = $utf8ToTranslit[$bank][$newChar] ?? ($unknown ?? $c);

            $strTmp .= $newChar;
        }

        return $strTmp;
    }

    /**
     * Calculate the UTF-8 order for a character.
     *
     * @param string $char The character to calculate the order for.
     *
     * @return int|null The UTF-8 order or null if the character is invalid.
     */
    private static function calculateUtf8Order(string $char): ?int
    {
        $ordC0 = self::$ORD[$char[0]] ?? null;
        $ordC1 = self::$ORD[$char[1]] ?? null;
        $ordC2 = self::$ORD[$char[2]] ?? null;
        $ordC3 = self::$ORD[$char[3]] ?? null;

        // Handle multi-byte UTF-8 characters
        if ($ordC0 >= 192 && $ordC0 <= 223) {
            return ($ordC0 - 192) * 64 + ($ordC1 - 128);
        }

        if ($ordC0 >= 224 && $ordC0 <= 239) {
            return ($ordC0 - 224) * 4096 + ($ordC1 - 128) * 64 + ($ordC2 - 128);
        }

        if ($ordC0 >= 240 && $ordC0 <= 247) {
            return ($ordC0 - 240) * 262144 + ($ordC1 - 128) * 4096 + ($ordC2 - 128) * 64 + ($ordC3 - 128);
        }

        return null;
    }

    /**
     * WARNING: This method will return broken characters and is only for special cases.
     *
     * Convert a UTF-8 encoded string to a single-byte string suitable for
     * functions that need the same string length after the conversion.
     *
     * The function simply uses (and updates) a tailored dynamic encoding
     * (in/out map parameter) where non-ASCII characters are remapped to
     * the range [128-255] in order of appearance.
     *
     * Thus, it supports up to 128 different multibyte code points max over
     * the whole set of strings sharing this encoding.
     *
     * Source: https://github.com/KEINOS/mb_levenshtein
     *
     * @param string $str UTF-8 string to be converted to extended ASCII.
     * @param array  $map Internal map of code points to ASCII characters.
     *
     * @return string Mapped broken string.
     *
     * @phpstan-param array<string, string> $map
     */
    private static function toAsciiRemapIntern(string $str, array &$map): string
    {
        // Find all UTF-8 characters
        $matches = [];
        if (!preg_match_all('/[\xC0-\xF7][\x80-\xBF]+/', $str, $matches)) {
            return $str; // Plain ASCII string
        }

        // Update the encoding map with characters not already encountered
        $mapCount = count($map);
        foreach ($matches[0] as $mbc) {
            if (!isset($map[$mbc])) {
                $map[$mbc] = chr(128 + $mapCount);
                ++$mapCount;
            }
        }

        // Finally, remap non-ASCII characters
        return strtr($str, $map);
    }

    /**
     * Get the language from a string.
     *
     * e.g.: de_at -> de_at
     *       de_DE -> de
     *       DE_DE -> de
     *       de-de -> de
     *
     * @return string
     */
    private static function getLanguage(string $language): string
    {
        if ($language === '') {
            return '';
        }

        // If there are no delimiters, simply return the lowercase version of the language.
        if (strpos($language, '_') === false && strpos($language, '-') === false) {
            return strtolower($language);
        }

        // Replace '-' with '_' and convert to lowercase
        $language = str_replace('-', '_', strtolower($language));

        // Remove the language region part (if it exists)
        $regex = '/(?<first>[a-z]+)_(?=\1)/';

        return (string) preg_replace($regex, '$1', $language);
    }

    /**
     * Get data from "/data/*.php".
     *
     * @return array<array-key,mixed>
     */
    private static function getData(string $file)
    {
        return include __DIR__ . '/data/' . $file . '.php';
    }

    /**
     * Get data from "/data/*.php".
     *
     * @return array<array-key,mixed>
     */
    private static function getDataIfExists(string $file): array
    {
        $file = __DIR__ . '/data/' . $file . '.php';
        if (\is_file($file)) {
            return include $file;
        }

        return [];
    }

    /**
     * @return void
     */
    private static function prepareAsciiAndExtrasMaps()
    {
        if (self::$ASCII_MAPS_AND_EXTRAS === null) {
            self::prepareAsciiMaps();
            self::prepareAsciiExtras();

            self::$ASCII_MAPS_AND_EXTRAS = \array_merge_recursive(
                self::$ASCII_MAPS ?? [],
                self::$ASCII_EXTRAS ?? []
            );
        }
    }

    /**
     * @return void
     */
    private static function prepareAsciiMaps()
    {
        if (self::$ASCII_MAPS === null) {
            self::$ASCII_MAPS = self::getData('ascii_by_languages');
        }
    }

    /**
     * @return void
     */
    private static function prepareAsciiExtras()
    {
        if (self::$ASCII_EXTRAS === null) {
            self::$ASCII_EXTRAS = self::getData('ascii_extras_by_languages');
        }
    }
}
