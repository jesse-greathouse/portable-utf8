# String Encoding Helpers

## Description

It is written in PHP (PHP 8+) and can work without "mbstring", "iconv" or any other extra encoding php-extension on your server.

Falls back to Symfony Polyfills. (https://github.com/symfony/polyfill)

The project based on ...
+ Voku's work - [portable-utf8](http://pageconfig.com/attachments/portable-utf8.php) 
+ Hamid Sarfraz's work - [portable-utf8](http://pageconfig.com/attachments/portable-utf8.php) 
+ Nicolas Grekas's work - [tchwork/utf8](https://github.com/tchwork/utf8) 
+ Behat's work - [Behat/Transliterator](https://github.com/Behat/Transliterator) 
+ Sebastián Grignoli's work - [neitanod/forceutf8](https://github.com/neitanod/forceutf8) 
+ Ivan Enderlin's work - [hoaproject/Ustring](https://github.com/hoaproject/Ustring)


## Demo

Here you can test some basic functions from this library and you can compare some results with the native php function results.

+ [encoder.suckup.de](https://encoder.suckup.de/)

## Index

* [Alternative](#alternative)
* [Install](#install-portable-utf-8-via-composer-require)
* [Why Portable UTF-8?](#why-portable-utf-8)
* [Requirements and Recommendations](#requirements-and-recommendations)
* [Warning](#warning)
* [Usage](#usage)
* [Class methods](#class-methods)
* [Unit Test](#unit-test)
* [License and Copyright](#license-and-copyright)

## Alternative

If you like a more Object Oriented Way to edit strings, then you can take a look at [jessegreathouse/Stringy](https://github.com/jessegreathouse/Stringy), it's a fork of "danielstjules/Stringy" but it used the "Portable UTF-8"-Class and some extra methods. 

```php
// Standard library
strtoupper('fòôbàř');       // 'FòôBàř'
strlen('fòôbàř');           // 10

// mbstring 
// WARNING: if you don't use a polyfill like "Portable UTF-8", you need to install the php-extension "mbstring" on your server
mb_strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
mb_strlen('fòôbàř');        // '6'

// Portable UTF-8
use jessegreathouse\helper\UTF8;
UTF8::strtoupper('fòôbàř');    // 'FÒÔBÀŘ'
UTF8::strlen('fòôbàř');        // '6'

// jessegreathouse/Stringy
use Stringy\Stringy as S;
$stringy = S::create('fòôbàř');
$stringy->toUpperCase();    // 'FÒÔBÀŘ'
$stringy->length();         // '6'
```


## Install "Portable UTF-8" via "composer require"
```shell
composer require jessegreathouse/portable-utf8
```

If your project do not need some of the Symfony polyfills please use the `replace` section of your `composer.json`. 
This removes any overhead from these polyfills as they are no longer part of your project. e.g.:
```json
{
  "replace": {
    "symfony/polyfill-php72": "1.99",
    "symfony/polyfill-iconv": "1.99",
    "symfony/polyfill-intl-grapheme": "1.99",
    "symfony/polyfill-intl-normalizer": "1.99",
    "symfony/polyfill-mbstring": "1.99"
  }
}
```

##  Why Portable UTF-8?[]()
PHP 5 and earlier versions have no native Unicode support. To bridge the gap, there exist several extensions like "mbstring", "iconv" and "intl".

The problem with "mbstring" and others is that most of the time you cannot ensure presence of a specific one on a server. If you rely on one of these, your application is no more portable. This problem gets even severe for open source applications that have to run on different servers with different configurations. Considering these, I decided to write a library:

## Requirements and Recommendations

*   No extensions are required to run this library. Portable UTF-8 only needs PCRE library that is available by default since PHP 4.2.0 and cannot be disabled since PHP 5.3.0. "\u" modifier support in PCRE for UTF-8 handling is not a must.
*   PHP 5.3 is the minimum requirement, and all later versions are fine with Portable UTF-8.
*   PHP 7.0 is the minimum requirement since version 4.0 of Portable UTF-8, otherwise composer will install an older version
*   PHP 8.0 support is also available and will adapt the behaviours of the native functions.
*   To speed up string handling, it is recommended that you have "mbstring" or "iconv" available on your server, as well as the latest version of PCRE library
*   Although Portable UTF-8 is easy to use; moving from native API to Portable UTF-8 may not be straight-forward for everyone. It is highly recommended that you do not update your scripts to include Portable UTF-8 or replace or change anything before you first know the reason and consequences. Most of the time, some native function may be all what you need.
*   There is also a shim for "mbstring", "iconv" and "intl", so you can use it also on shared webspace. 

## Usage

Example 1: UTF8::cleanup()
```php
  echo UTF8::cleanup('�DÃ¼sseldorf�');
  
  // will output:
  // Düsseldorf
```

Example 2: UTF8::strlen()
```php
  $string = 'string <strong>with utf-8 chars åèä</strong> - doo-bee doo-bee dooh';

  echo strlen($string) . "\n<br />";
  echo UTF8::strlen($string) . "\n<br />";

  // will output:
  // 70
  // 67

  $string_test1 = strip_tags($string);
  $string_test2 = UTF8::strip_tags($string);

  echo strlen($string_test1) . "\n<br />";
  echo UTF8::strlen($string_test2) . "\n<br />";

  // will output:
  // 53
  // 50
```

Example 3: UTF8::fixUtf8()
```php

  echo UTF8::fixUtf8('DÃ¼sseldorf');
  echo UTF8::fixUtf8('Ã¤');
  
  // will output:
  // Düsseldorf
  // ä
```

# Portable UTF-8 | API

The API from the "UTF8"-Class is written as small static methods that will match the default PHP-API.


## Class methods

<p id="jessegreathouse-php-readme-class-methods"></p><table><tr><td><a href="#accessstring-str-int-pos-string-encoding-string">access</a>
</td><td><a href="#addBomToStringstring-str-non-empty-string">addBomToString</a>
</td><td><a href="#changeArrayKeyCasearray-array-int-case-string-encoding-string">changeArrayKeyCase</a>
</td><td><a href="#getSubstringBetweenstring-str-string-start-string-end-int-offset-string-encoding-string">getSubstringBetween</a>
</td></tr><tr><td><a href="#binaryToStringstring-bin-string">binaryToString</a>
</td><td><a href="#bom-non-empty-string">bom</a>
</td><td><a href="#callbackcallablestring-string-callback-string-str-string">callback</a>
</td><td><a href="#charAtstring-str-int-index-string-encoding-string">charAt</a>
</td></tr><tr><td><a href="#charsstring-str-string">chars</a>
</td><td><a href="#checkforsupport-truenull">checkForSupport</a>
</td><td><a href="#chrint-code_point-string-encoding-stringnull">chr</a>
</td><td><a href="#chrMapcallablestring-string-callback-string-str-string">chrMap</a>
</td></tr><tr><td><a href="#chrSizeListstring-str-int">chrSizeList</a>
</td><td><a href="#chrToDecimalstring-char-int">chrToDecimal</a>
</td><td><a href="#chrToHexintstring-char-string-prefix-string">chrToHex</a>
</td><td><a href="#chunkSplitstring-str-int-chunk_length-string-end-string">chunkSplit</a>
</td></tr><tr><td><a href="#cleanstring-str-bool-removeBom-bool-normalizeWhitespace-bool-normalizeMsWord-bool-keep_non_breaking_space-bool-replaceDiamondQuestionMark-bool-removeInvisibleCharacters-bool-removeInvisibleCharacters_url_encoded-string">clean</a>
</td><td><a href="#cleanupstring-str-string">cleanup</a>
</td><td><a href="#codepointsstringstring-arg-bool-use_u_style-intstring">codepoints</a>
</td><td><a href="#collapseWhitespacestring-str-string">collapseWhitespace</a>
</td></tr><tr><td><a href="#countCharsstring-str-bool-clean_utf8-bool-try_to_use_mb_functions-int">countChars</a>
</td><td><a href="#cssIdentifierstring-str-string-filter-bool-strip_tags-bool-strtolower-string">cssIdentifier</a>
</td><td><a href="#cssStripMediaQueriesstring-str-string">cssStripMediaQueries</a>
</td><td><a href="#ctype_loaded-bool">ctype_loaded</a>
</td></tr><tr><td><a href="#decimalToChrintstring-int-string">decimalToChr</a>
</td><td><a href="#decodeMimeHeaderstring-str-string-encoding-falsestring">decodeMimeHeader</a>
</td><td><a href="#emojiDecodestring-str-bool-use_reversible_string_mappings-string">emojiDecode</a>
</td><td><a href="#emojiEncodestring-str-bool-use_reversible_string_mappings-string">emojiEncode</a>
</td></tr><tr><td><a href="#emojiFromCountryCodestring-country_code_iso_3166_1-string">emojiFromCountryCode</a>
</td><td><a href="#encodestring-to_encoding-string-str-bool-auto_detect_the_from_encoding-string-from_encoding-string">encode</a>
</td><td><a href="#encodeMimeHeaderstring-str-string-from_charset-string-to_charset-string-transfer_encoding-string-linefeed-int-indent-falsestring">encodeMimeHeader</a>
</td><td><a href="#extractTextstring-str-string-search-intnull-length-string-replacer_for_skipped_text-string-encoding-string">extractText</a>
</td></tr><tr><td><a href="#file_get_contentsstring-filename-bool-use_include_path-resourcenull-context-intnull-offset-intnull-max_length-int-timeout-bool-convert_toUtf8-string-from_encoding-falsestring">file_get_contents</a>
</td><td><a href="#fileHasBomstring-file_path-bool">fileHasBom</a>
</td><td><a href="#filterarrayobjectstring-var-int-normalization_form-string-leading_combining-mixed">filter</a>
</td><td><a href="#filter_inputint-type-string-variable_name-int-filter-intintnull-options-mixed">filter_input</a>
</td></tr><tr><td><a href="#filter_input_arrayint-type-arraynull-definition-bool-add_empty-arraystringmixedfalsenull">filter_input_array</a>
</td><td><a href="#filter_varfloatintstringnull-variable-int-filter-intint-options-mixed">filter_var</a>
</td><td><a href="#filter_var_arrayarray-data-arrayint-definition-bool-add_empty-arraystringmixedfalsenull">filter_var_array</a>
</td><td><a href="#finfo_loaded-bool">finfo_loaded</a>
</td></tr><tr><td><a href="#firstCharstring-str-int-n-string-encoding-string">firstChar</a>
</td><td><a href="#fitsInsidestring-str-int-box_size-bool">fitsInside</a>
</td><td><a href="#fixSimpleUtf8string-str-string">fixSimpleUtf8</a>
</td><td><a href="#fixUtf8stringstring-str-stringstring">fixUtf8</a>
</td></tr><tr><td><a href="#getchardirectionstring-char-string">getCharDirection</a>
</td><td><a href="#getsupportinfostringnull-key-mixed">getSupportInfo</a>
</td><td><a href="#geturlparamfromarraystring-param-array-data-mixed">getUrlParamFromArray</a>
</td><td><a href="#getFileTypestring-str-array-fallback">getFileType</a>
</td></tr><tr><td><a href="#getRandomStringint-length-string-possible_chars-string-encoding-string">getRandomString</a>
</td><td><a href="#getUniqueStringintstring-extra_entropy-bool-use_md5-non-empty-string">getUniqueString</a>
</td><td><a href="#hasLowercasestring-str-bool">hasLowercase</a>
</td><td><a href="#hasUppercasestring-str-bool">hasUppercase</a>
</td></tr><tr><td><a href="#has_whitespacestring-str-bool">has_whitespace</a>
</td><td><a href="#hexToChrstring-hexdec-string">hexToChr</a>
</td><td><a href="#hexToIntstring-hexdec-falseint">hexToInt</a>
</td><td><a href="#html_encodestring-str-bool-keep_ascii_chars-string-encoding-string">html_encode</a>
</td></tr><tr><td><a href="#html_entity_decodestring-str-intnull-flags-string-encoding-string">html_entity_decode</a>
</td><td><a href="#htmlEscapestring-str-string-encoding-string">htmlEscape</a>
</td><td><a href="#htmlStripEmptyTagsstring-str-string">htmlStripEmptyTags</a>
</td><td><a href="#htmlentitiesstring-str-int-flags-string-encoding-bool-double_encode-string">htmlentities</a>
</td></tr><tr><td><a href="#htmlspecialcharsstring-str-int-flags-string-encoding-bool-double_encode-string">htmlspecialchars</a>
</td><td><a href="#iconv_loaded-bool">iconv_loaded</a>
</td><td><a href="#intToHexint-int-string-prefix-string">intToHex</a>
</td><td><a href="#intlchar_loaded-bool">intlChar_loaded</a>
</td></tr><tr><td><a href="#intl_loaded-bool">intl_loaded</a>
</td><td><a href="#isAlphastring-str-bool">isAlpha</a>
</td><td><a href="#isAlphanumericstring-str-bool">isAlphanumeric</a>
</td><td><a href="#isAsciistring-str-bool">isAscii</a>
</td></tr><tr><td><a href="#isBase64stringnull-str-bool-empty_string_is_valid-bool">isBase64</a>
</td><td><a href="#is_binaryintstring-input-bool-strict-bool">is_binary</a>
</td><td><a href="#isBinaryFilestring-file-bool">isBinaryFile</a>
</td><td><a href="#isBlankstring-str-bool">isBlank</a>
</td></tr><tr><td><a href="#isBomstring-str-bool">isBom</a>
</td><td><a href="#isEmptyarrayfloatintstring-str-bool">isEmpty</a>
</td><td><a href="#isHexadecimalstring-str-bool">isHexadecimal</a>
</td><td><a href="#isHtmlstring-str-bool">isHtml</a>
</td></tr><tr><td><a href="#is_jsonstring-str-bool-only_array_or_object_results_are_valid-bool">is_json</a>
</td><td><a href="#isLowercasestring-str-bool">isLowercase</a>
</td><td><a href="#isPrintablestring-str-bool-ignore_control_characters-bool">isPrintable</a>
</td><td><a href="#isPunctuationstring-str-bool">isPunctuation</a>
</td></tr><tr><td><a href="#isSerializedstring-str-bool">isSerialized</a>
</td><td><a href="#isUppercasestring-str-bool">isUppercase</a>
</td><td><a href="#isUrlstring-url-bool-disallow_localhost-bool">isUrl</a>
</td><td><a href="#isUtf8intstringstringnull-str-bool-strict-bool">isUtf8</a>
</td></tr><tr><td><a href="#isUtf16string-str-bool-check_if_string_is_binary-falseint">isUtf16</a>
</td><td><a href="#isUtf32string-str-bool-check_if_string_is_binary-falseint">isUtf32</a>
</td><td><a href="#jsonDecodestring-json-bool-assoc-int-depth-int-options-mixed">jsonDecode</a>
</td><td><a href="#json_encodemixed-value-int-options-int-depth-falsestring">json_encode</a>
</td></tr><tr><td><a href="#json_loaded-bool">json_loaded</a>
</td><td><a href="#lcfirststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">lcfirst</a>
</td><td><a href="#lcwordsstring-str-string-exceptions-string-char_list-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">lcwords</a>
</td><td><a href="#levenshteinstring-str1-string-str2-int-insertioncost-int-replacementcost-int-deletioncost-int">levenshtein</a>
</td></tr><tr><td><a href="#ltrimstring-str-stringnull-chars-string">ltrim</a>
</td><td><a href="#maxstringstring-arg-stringnull">max</a>
</td><td><a href="#maxChrWidthstring-str-int">maxChrWidth</a>
</td><td><a href="#mbstring_loaded-bool">mbstring_loaded</a>
</td></tr><tr><td><a href="#minstringstring-arg-stringnull">min</a>
</td><td><a href="#normalizeEncodingmixed-encoding-mixed-fallback-mixedstring">normalizeEncoding</a>
</td><td><a href="#normalizeLineEndingstring-str-stringstring-replacer-string">normalizeLineEnding</a>
</td><td><a href="#normalizeMsWordstring-str-string">normalizeMsWord</a>
</td></tr><tr><td><a href="#normalizeWhitespacestring-str-bool-keep_non_breaking_space-bool-keep_bidi_unicode_controls-bool-normalize_control_characters-string">normalizeWhitespace</a>
</td><td><a href="#ordstring-chr-string-encoding-int">ord</a>
</td><td><a href="#parse_strstring-str-array-result-bool-clean_utf8-bool">parse_str</a>
</td><td><a href="#pcre_utf8_support-bool">pcre_utf8_support</a>
</td></tr><tr><td><a href="#rangeintstring-var1-intstring-var2-bool-use_ctype-string-encoding-floatint-step-liststring">range</a>
</td><td><a href="#rawurldecodestring-str-bool-multi_decode-string">rawurldecode</a>
</td><td><a href="#regexReplacestring-str-string-pattern-string-replacement-string-options-string-delimiter-string">regexReplace</a>
</td><td><a href="#removeBomstring-str-string">removeBom</a>
</td></tr><tr><td><a href="#remove_duplicatesstring-str-stringstring-what-string">remove_duplicates</a>
</td><td><a href="#remove_htmlstring-str-string-allowable_tags-string">remove_html</a>
</td><td><a href="#remove_html_breaksstring-str-string-replacement-string">remove_html_breaks</a>
</td><td><a href="#remove_ileftstring-str-string-substring-string-encoding-string">remove_ileft</a>
</td></tr><tr><td><a href="#removeInvisibleCharactersstring-str-bool-url_encoded-string-replacement-bool-keep_basic_control_characters-string">removeInvisibleCharacters</a>
</td><td><a href="#remove_irightstring-str-string-substring-string-encoding-string">remove_iright</a>
</td><td><a href="#remove_leftstring-str-string-substring-string-encoding-string">remove_left</a>
</td><td><a href="#remove_rightstring-str-string-substring-string-encoding-string">remove_right</a>
</td></tr><tr><td><a href="#replacestring-str-string-search-string-replacement-bool-case_sensitive-string">replace</a>
</td><td><a href="#replace_allstring-str-string-search-stringstring-replacement-bool-case_sensitive-string">replace_all</a>
</td><td><a href="#replaceDiamondQuestionMarkstring-str-string-replacement_char-bool-process_invalid_utf8_chars-string">replaceDiamondQuestionMark</a>
</td><td><a href="#rtrimstring-str-stringnull-chars-string">rtrim</a>
</td></tr><tr><td><a href="#showsupportbool-useecho-stringvoid">showSupport</a>
</td><td><a href="#single_chr_html_encodestring-char-bool-keep_ascii_chars-string-encoding-string">single_chr_html_encode</a>
</td><td><a href="#spaces_to_tabsstring-str-int-tab_length-string">spaces_to_tabs</a>
</td><td><a href="#str_camelizestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">str_camelize</a>
</td></tr><tr><td><a href="#str_capitalize_namestring-str-string">str_capitalize_name</a>
</td><td><a href="#strContainsstring-haystack-string-needle-bool-case_sensitive-bool">strContains</a>
</td><td><a href="#strContainsAllstring-haystack-scalar-needles-bool-case_sensitive-bool">strContainsAll</a>
</td><td><a href="#strContainsAnystring-haystack-scalar-needles-bool-case_sensitive-bool">strContainsAny</a>
</td></tr><tr><td><a href="#strDelimitstring-str-string-encoding-string">strDelimit</a>
</td><td><a href="#strDelimitstring-str-string-delimiter-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">strDelimit</a>
</td><td><a href="#detectStringEncodingstring-str-falsestring">detectStringEncoding</a>
</td><td><a href="#str_ends_withstring-haystack-string-needle-bool">str_ends_with</a>
</td></tr><tr><td><a href="#strEndsWithAnystring-str-string-substrings-bool">strEndsWithAny</a>
</td><td><a href="#strEnsureLeftstring-str-string-substring">strEnsureLeft</a>
</td><td><a href="#strEnsureRightstring-str-string-substring-string">strEnsureRight</a>
</td><td><a href="#strHumanizestring-str-string">strHumanize</a>
</td></tr><tr><td><a href="#strEndsWithInsensitivestring-haystack-string-needle-bool">strEndsWithInsensitive</a>
</td><td><a href="#strEndsWithAnyInsensitivestring-str-string-substrings-bool">strEndsWithAnyInsensitive</a>
</td><td><a href="#strInsertstring-str-string-substring-int-index-string-encoding-string">strInsert</a>
</td><td><a href="#strReplaceInsensitivestringstring-search-stringstring-replacement-stringstring-subject-int-count-stringstring">strReplaceInsensitive</a>
</td></tr><tr><td><a href="#strReplaceInsensitive_beginningstring-str-string-search-string-replacement-string">strReplaceInsensitive_beginning</a>
</td><td><a href="#strReplaceEndingInsensitivestring-str-string-search-string-replacement-string">strReplaceEndingInsensitive</a>
</td><td><a href="#strStartsWithInsensitivestring-haystack-string-needle-bool">strStartsWithInsensitive</a>
</td><td><a href="#strStartsWithAnyInsensitivestring-str-scalar-substrings-bool">strStartsWithAnyInsensitive</a>
</td></tr><tr><td><a href="#strSubstrAfterFirstSeparatorInsensitivestring-str-string-separator-string-encoding-string">strSubstrAfterFirstSeparatorInsensitive</a>
</td><td><a href="#strSubstrAfterLastSeparatorInsensitivestring-str-string-separator-string-encoding-string">strSubstrAfterLastSeparatorInsensitive</a>
</td><td><a href="#strSubstrBeforeFirstSeparatorInsensitivestring-str-string-separator-string-encoding-string">strSubstrBeforeFirstSeparatorInsensitive</a>
</td><td><a href="#strSubstrBeforeLastSeparatorInsensitivestring-str-string-separator-string-encoding-string">strSubstrBeforeLastSeparatorInsensitive</a>
</td></tr><tr><td><a href="#strSubstrFirstInsensitivestring-str-string-needle-bool-before_needle-string-encoding-string">strSubstrFirstInsensitive</a>
</td><td><a href="#strSubstrLastInsensitivestring-str-string-needle-bool-before_needle-string-encoding-string">strSubstrLastInsensitive</a>
</td><td><a href="#strLastCharstring-str-int-n-string-encoding-string">strLastChar</a>
</td><td><a href="#str_limitstring-str-int-length-string-str_add_on-string-encoding-string">str_limit</a>
</td></tr><tr><td><a href="#strLimitAfterWordstring-str-int-length-string-str_add_on-string-encoding-string">strLimitAfterWord</a>
</td><td><a href="#strLongestCommonPrefixstring-str1-string-str2-string-encoding-string">strLongestCommonPrefix</a>
</td><td><a href="#strLongestCommonSubstringstring-str1-string-str2-string-encoding-string">strLongestCommonSubstring</a>
</td><td><a href="#strLongestCommonSuffixstring-str1-string-str2-string-encoding-string">strLongestCommonSuffix</a>
</td></tr><tr><td><a href="#strMatchesPatternstring-str-string-pattern-bool">strMatchesPattern</a>
</td><td><a href="#str_obfuscatestring-str-float-percent-string-obfuscatechar-string-keepchars-string">str_obfuscate</a>
</td><td><a href="#strOffsetExistsstring-str-int-offset-string-encoding-bool">strOffsetExists</a>
</td><td><a href="#strOffsetGetstring-str-int-index-string-encoding-string">strOffsetGet</a>
</td></tr><tr><td><a href="#str_padstring-str-int-pad_length-string-pad_string-intstring-pad_type-string-encoding-string">str_pad</a>
</td><td><a href="#strPadBothstring-str-int-length-string-pad_str-string-encoding-string">strPadBoth</a>
</td><td><a href="#strPadLeftstring-str-int-length-string-pad_str-string-encoding-string">strPadLeft</a>
</td><td><a href="#strPadRightstring-str-int-length-string-pad_str-string-encoding-string">strPadRight</a>
</td></tr><tr><td><a href="#str_repeatstring-str-int-multiplier-string">str_repeat</a>
</td><td><a href="#str_replace_beginningstring-str-string-search-string-replacement-string">str_replace_beginning</a>
</td><td><a href="#str_replace_endingstring-str-string-search-string-replacement-string">str_replace_ending</a>
</td><td><a href="#str_replace_firststring-search-string-replace-string-subject-string">str_replace_first</a>
</td></tr><tr><td><a href="#str_replace_laststring-search-string-replace-string-subject-string">str_replace_last</a>
</td><td><a href="#str_shufflestring-str-string-encoding-string">str_shuffle</a>
</td><td><a href="#str_slicestring-str-int-start-intnull-end-string-encoding-falsestring">str_slice</a>
</td><td><a href="#str_snakeizestring-str-string-encoding-string">str_snakeize</a>
</td></tr><tr><td><a href="#str_sortstring-str-bool-unique-bool-desc-string">str_sort</a>
</td><td><a href="#strSplitintstring-str-int-length-bool-clean_utf8-bool-try_to_use_mb_functions-liststring">strSplit</a>
</td><td><a href="#strSplitArrayintstring-input-int-length-bool-clean_utf8-bool-try_to_use_mb_functions-listliststring">strSplitArray</a>
</td><td><a href="#strSplit_patternstring-str-string-pattern-int-limit-string">strSplit_pattern</a>
</td></tr><tr><td><a href="#str_starts_withstring-haystack-string-needle-bool">str_starts_with</a>
</td><td><a href="#str_starts_with_anystring-str-scalar-substrings-bool">str_starts_with_any</a>
</td><td><a href="#str_substr_after_first_separatorstring-str-string-separator-string-encoding-string">str_substr_after_first_separator</a>
</td><td><a href="#str_substr_after_last_separatorstring-str-string-separator-string-encoding-string">str_substr_after_last_separator</a>
</td></tr><tr><td><a href="#str_substr_before_first_separatorstring-str-string-separator-string-encoding-string">str_substr_before_first_separator</a>
</td><td><a href="#str_substr_before_last_separatorstring-str-string-separator-string-encoding-string">str_substr_before_last_separator</a>
</td><td><a href="#str_substr_firststring-str-string-needle-bool-before_needle-string-encoding-string">str_substr_first</a>
</td><td><a href="#str_substr_laststring-str-string-needle-bool-before_needle-string-encoding-string">str_substr_last</a>
</td></tr><tr><td><a href="#str_surroundstring-str-string-substring-string">str_surround</a>
</td><td><a href="#str_titleizestring-str-stringnull-ignore-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-bool-use_trim_first-stringnull-word_define_chars-string">str_titleize</a>
</td><td><a href="#str_titleize_for_humansstring-str-string-ignore-string-encoding-string">str_titleize_for_humans</a>
</td><td><a href="#str_to_binarystring-str-falsestring">str_to_binary</a>
</td></tr><tr><td><a href="#str_to_linesstring-str-bool-remove_empty_values-intnull-remove_short_values-string">str_to_lines</a>
</td><td><a href="#strToWordsstring-str-string-char_list-bool-remove_empty_values-intnull-remove_short_values-liststring">strToWords</a>
</td><td><a href="#str_truncatestring-str-int-length-string-substring-string-encoding-string">str_truncate</a>
</td><td><a href="#str_truncate_safestring-str-int-length-string-substring-string-encoding-bool-ignore_do_not_split_words_for_one_word-string">str_truncate_safe</a>
</td></tr><tr><td><a href="#str_underscoredstring-str-string">str_underscored</a>
</td><td><a href="#str_upper_camelizestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">str_upper_camelize</a>
</td><td><a href="#str_word_countstring-str-int-format-string-char_list-intstring">str_word_count</a>
</td><td><a href="#strcasecmpstring-str1-string-str2-string-encoding-int">strcasecmp</a>
</td></tr><tr><td><a href="#strcmpstring-str1-string-str2-int">strcmp</a>
</td><td><a href="#strcspnstring-str-string-char_list-int-offset-intnull-length-string-encoding-int">strcspn</a>
</td><td><a href="#stringintintstringstring-intorhex-string">string</a>
</td><td><a href="#hasBomstring-str-bool">hasBom</a>
</td></tr><tr><td><a href="#strip_tagsstring-str-stringnull-allowable_tags-bool-clean_utf8-string">strip_tags</a>
</td><td><a href="#strip_whitespacestring-str-string">strip_whitespace</a>
</td><td><a href="#striposstring-haystack-string-needle-int-offset-string-encoding-bool-clean_utf8-falseint">stripos</a>
</td><td><a href="#stripos_in_bytestring-haystack-string-needle-int-offset-falseint">stripos_in_byte</a>
</td></tr><tr><td><a href="#stristrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-falsestring">stristr</a>
</td><td><a href="#strlenstring-str-string-encoding-bool-clean_utf8-falseint">strlen</a>
</td><td><a href="#strlenInBytestring-str-int">strlenInByte</a>
</td><td><a href="#strnatcasecmpstring-str1-string-str2-string-encoding-int">strnatcasecmp</a>
</td></tr><tr><td><a href="#strnatcmpstring-str1-string-str2-int">strnatcmp</a>
</td><td><a href="#strncasecmpstring-str1-string-str2-int-len-string-encoding-int">strncasecmp</a>
</td><td><a href="#strncmpstring-str1-string-str2-int-len-string-encoding-int">strncmp</a>
</td><td><a href="#strpbrkstring-haystack-string-char_list-falsestring">strpbrk</a>
</td></tr><tr><td><a href="#strposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-falseint">strpos</a>
</td><td><a href="#strpos_in_bytestring-haystack-string-needle-int-offset-falseint">strpos_in_byte</a>
</td><td><a href="#strrchrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-falsestring">strrchr</a>
</td><td><a href="#strrevstring-str-string-encoding-string">strrev</a>
</td></tr><tr><td><a href="#strrichrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-falsestring">strrichr</a>
</td><td><a href="#strriposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-falseint">strripos</a>
</td><td><a href="#strripos_in_bytestring-haystack-string-needle-int-offset-falseint">strripos_in_byte</a>
</td><td><a href="#strrposstring-haystack-intstring-needle-int-offset-string-encoding-bool-clean_utf8-falseint">strrpos</a>
</td></tr><tr><td><a href="#strrpos_in_bytestring-haystack-string-needle-int-offset-falseint">strrpos_in_byte</a>
</td><td><a href="#strspnstring-str-string-mask-int-offset-intnull-length-string-encoding-falseint">strspn</a>
</td><td><a href="#strstrstring-haystack-string-needle-bool-before_needle-string-encoding-bool-clean_utf8-falsestring">strstr</a>
</td><td><a href="#strstr_in_bytestring-haystack-string-needle-bool-before_needle-falsestring">strstr_in_byte</a>
</td></tr><tr><td><a href="#strtocasefoldstring-str-bool-full-bool-clean_utf8-string-encoding-stringnull-lang-bool-lower-string">strtocasefold</a>
</td><td><a href="#strtolowerstring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">strtolower</a>
</td><td><a href="#strtoupperstring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">strtoupper</a>
</td><td><a href="#strtrstring-str-stringstring-from-stringstring-to-string">strtr</a>
</td></tr><tr><td><a href="#strwidthstring-str-string-encoding-bool-clean_utf8-int">strwidth</a>
</td><td><a href="#substrstring-str-int-offset-intnull-length-string-encoding-bool-clean_utf8-falsestring">substr</a>
</td><td><a href="#substr_comparestring-str1-string-str2-int-offset-intnull-length-bool-case_insensitivity-string-encoding-int">substr_compare</a>
</td><td><a href="#substr_countstring-haystack-string-needle-int-offset-intnull-length-string-encoding-bool-clean_utf8-falseint">substr_count</a>
</td></tr><tr><td><a href="#substr_count_in_bytestring-haystack-string-needle-int-offset-intnull-length-falseint">substr_count_in_byte</a>
</td><td><a href="#countSubstringstring-str-string-substring-bool-case_sensitive-string-encoding-int">countSubstring</a>
</td><td><a href="#substr_ileftstring-haystack-string-needle-string">substr_ileft</a>
</td><td><a href="#strlenInBytestring-str-int-offset-intnull-length-falsestring">strlenInByte</a>
</td></tr><tr><td><a href="#substr_irightstring-haystack-string-needle-string">substr_iright</a>
</td><td><a href="#substr_leftstring-haystack-string-needle-string">substr_left</a>
</td><td><a href="#substr_replacestringstring-str-stringstring-replacement-intint-offset-intintnull-length-string-encoding-stringstring">substr_replace</a>
</td><td><a href="#substr_rightstring-haystack-string-needle-string-encoding-string">substr_right</a>
</td></tr><tr><td><a href="#swapcasestring-str-string-encoding-bool-clean_utf8-string">swapCase</a>
</td><td><a href="#symfony_polyfill_used-bool">symfony_polyfill_used</a>
</td><td><a href="#tabs_to_spacesstring-str-int-tab_length-string">tabs_to_spaces</a>
</td><td><a href="#titlecasestring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">titlecase</a>
</td></tr><tr><td><a href="#toAsciistring-str-string-unknown-bool-strict-string">toAscii</a>
</td><td><a href="#to_booleanboolfloatintstring-str-bool">to_boolean</a>
</td><td><a href="#to_filenamestring-str-bool-use_transliterate-string-fallback_char-string">to_filename</a>
</td><td><a href="#to_intstring-str-intnull">to_int</a>
</td></tr><tr><td><a href="#toIso8859stringstring-str-stringstring">toIso8859</a>
</td><td><a href="#to_stringfloatintobjectstringnull-input-stringnull">to_string</a>
</td><td><a href="#toUtf8stringstring-str-bool-decode_html_entity_toUtf8-stringstring">toUtf8</a>
</td><td><a href="#toUtf8Stringstring-str-bool-decode_html_entity_toUtf8-string">toUtf8String</a>
</td></tr><tr><td><a href="#trimstring-str-stringnull-chars-string">trim</a>
</td><td><a href="#ucfirststring-str-string-encoding-bool-clean_utf8-stringnull-lang-bool-try_to_keep_the_string_length-string">ucfirst</a>
</td><td><a href="#ucwordsstring-str-string-exceptions-string-char_list-string-encoding-bool-clean_utf8-string">ucwords</a>
</td><td><a href="#urldecodestring-str-bool-multi_decode-string">urldecode</a>
</td></tr><tr><td><a href="#utf8Decodestring-str-bool-keep_utf8_chars-string">utf8Decode</a>
</td><td><a href="#utf8_encodestring-str-string">utf8_encode</a>
</td><td><a href="#whitespace_table-string">whitespace_table</a>
</td><td><a href="#words_limitstring-str-int-limit-string-str_add_on-string">words_limit</a>
</td></tr><tr><td><a href="#wordwrapstring-str-int-width-string-break-bool-cut-string">wordwrap</a>
</td><td><a href="#wordwrap_per_linestring-str-int-width-string-break-bool-cut-bool-add_final_break-stringnull-delimiter-string">wordwrap_per_line</a>
</td><td><a href="#ws-string">ws</a>
</td></tr></table>

## charAt(string $str, int $pos, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Return the character at the specified position: $str[1] like functionality.

EXAMPLE: <code>UTF8::charAt('fòô', 1); // 'ò'</code>

**Parameters:**
- `string $str <p>A UTF-8 string.</p>`
- `int $pos <p>The position of character to return.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>Single multi-byte character.</p>`

--------

## addBomToString(string $str): non-empty-string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Prepends UTF-8 BOM character to the string and returns the whole string.

INFO: If BOM already existed there, the Input string is returned.

EXAMPLE: <code>UTF8::addBomToString('fòô'); // "\xEF\xBB\xBF" . 'fòô'</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `non-empty-string <p>The output string that contains BOM.</p>`

--------

## changeArrayKeyCase(array $array, int $case, string $encoding): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Changes all keys in an array.

**Parameters:**
- `array<string, mixed> $array <p>The array to work on</p>`
- `int $case [optional] <p> Either <strong>CASE_UPPER</strong><br>
or <strong>CASE_LOWER</strong> (default)</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string[] <p>An array with its keys lower- or uppercased.</p>`

--------

## getSubstringBetween(string $str, string $start, string $end, int $offset, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the substring getSubstringBetween $start and $end, if found, or an empty
string. An optional offset may be supplied from which to begin the
search for the start string.

**Parameters:**
- `string $str`
- `string $start <p>Delimiter marking the start of the substring.</p>`
- `string $end <p>Delimiter marking the end of the substring.</p>`
- `int $offset [optional] <p>Index from which to begin the search. Default: 0</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## binaryToString(string $bin): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert binary into a string.

INFO: opposite to UTF8::str_to_binary()

EXAMPLE: <code>UTF8::binaryToString('11110000100111111001100010000011'); // '😃'</code>

**Parameters:**
- `string $bin 1|0`

**Return:**
- `string`

--------

## bom(): non-empty-string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the UTF-8 Byte Order Mark Character.

INFO: take a look at UTF8::$bom for e.g. UTF-16 and UTF-32 BOM values

EXAMPLE: <code>UTF8::bom(); // "\xEF\xBB\xBF"</code>

**Parameters:**
__nothing__

**Return:**
- `non-empty-string <p>UTF-8 Byte Order Mark.</p>`

--------

## callback(callable(string): string $callback, string $str): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `callable(string): string $callback`
- `string $str`

**Return:**
- `string[]`

--------

**Parameters:**
- `string $str <p>The input string.</p>`
- `int<1, max> $index <p>Position of the character.</p>`
- `string $encoding [optional] <p>Default is UTF-8</p>`

**Return:**
- `string <p>The character at $index.</p>`

--------

## chars(string $str): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns an array consisting of the characters in the string.

**Parameters:**
- `T $str <p>The input string.</p>`

**Return:**
- `string[] <p>An array of chars.</p>`

--------

## checkForSupport(): true|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
This method will auto-detect your server environment for UTF-8 support.

**Parameters:**
__nothing__

**Return:**
- `true|null`

--------

## chr(int $code_point, string $encoding): string|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Generates a UTF-8 encoded character from the given code point.

INFO: opposite to UTF8::ord()

EXAMPLE: <code>UTF8::chr(0x2603); // '☃'</code>

**Parameters:**
- `int $code_point <p>The code point for which to generate a character.</p>`
- `string $encoding [optional] <p>Default is UTF-8</p>`

**Return:**
- `string|null <p>Multi-byte character, returns null on failure or empty input.</p>`

--------

## chrMap(callable(string): string $callback, string $str): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Applies callback to all characters of a string.

EXAMPLE: <code>UTF8::chrMap([UTF8::class, 'strtolower'], 'Κόσμε'); // ['κ','ό', 'σ', 'μ', 'ε']</code>

**Parameters:**
- `callable(string): string $callback`
- `string $str <p>UTF-8 string to run callback on.</p>`

**Return:**
- `string[] <p>The outcome of the callback, as array.</p>`

--------

## chrSizeList(string $str): int[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Generates an array of byte length of each character of a Unicode string.

1 byte => U+0000  - U+007F
2 byte => U+0080  - U+07FF
3 byte => U+0800  - U+FFFF
4 byte => U+10000 - U+10FFFF

EXAMPLE: <code>UTF8::chrSizeList('中文空白-test'); // [3, 3, 3, 3, 1, 1, 1, 1, 1]</code>

**Parameters:**
- `T $str <p>The original unicode string.</p>`

**Return:**
- `int[] <p>An array of byte lengths of each character.</p>`

--------

## chrToDecimal(string $char): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get a decimal code representation of a specific character.

INFO: opposite to UTF8::decimalToChr()

EXAMPLE: <code>UTF8::chrToDecimal('§'); // 0xa7</code>

**Parameters:**
- `string $char <p>The input character.</p>`

**Return:**
- `int`

--------

## chrToHex(int|string $char, string $prefix): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get hexadecimal code point (U+xxxx) of a UTF-8 encoded character.

EXAMPLE: <code>UTF8::chrToHex('§'); // U+00a7</code>

**Parameters:**
- `int|string $char <p>The input character</p>`
- `string $prefix [optional]`

**Return:**
- `string <p>The code point encoded as U+xxxx.</p>`

--------

## chunkSplit(string $str, int $chunk_length, string $end): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Splits a string into smaller chunks and multiple lines, using the specified line ending character.

EXAMPLE: <code>UTF8::chunkSplit('ABC-ÖÄÜ-中文空白-κόσμε', 3); // "ABC\r\n-ÖÄ\r\nÜ-中\r\n文空白\r\n-κό\r\nσμε"</code>

**Parameters:**
- `T $str <p>The original string to be split.</p>`
- `int<1, max> $chunk_length [optional] <p>The maximum character length of a chunk.</p>`
- `string $end [optional] <p>The character(s) to be inserted at the end of each chunk.</p>`

**Return:**
- `string <p>The chunked string.</p>`

--------

## clean(string $str, bool $removeBom, bool $normalizeWhitespace, bool $normalizeMsWord, bool $keep_non_breaking_space, bool $replaceDiamondQuestionMark, bool $removeInvisibleCharacters, bool $removeInvisibleCharacters_url_encoded): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Accepts a string and removes all non-UTF-8 characters from it + extras if needed.

EXAMPLE: <code>UTF8::clean("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - DÃ¼sseldorf'</code>

**Parameters:**
- `string $str <p>The string to be sanitized.</p>`
- `bool $removeBom [optional] <p>Set to true, if you need to remove
UTF-BOM.</p>`
- `bool $normalizeWhitespace [optional] <p>Set to true, if you need to normalize the
whitespace.</p>`
- `bool $normalizeMsWord [optional] <p>Set to true, if you need to normalize MS
Word chars e.g.: "…"
=> "..."</p>`
- `bool $keep_non_breaking_space [optional] <p>Set to true, to keep non-breaking-spaces,
in
combination with
$normalizeWhitespace</p>`
- `bool $replaceDiamondQuestionMark [optional] <p>Set to true, if you need to remove diamond
question mark e.g.: "�"</p>`
- `bool $removeInvisibleCharacters [optional] <p>Set to false, if you not want to remove
invisible characters e.g.: "\0"</p>`
- `bool $removeInvisibleCharacters_url_encoded [optional] <p>Set to true, if you not want to remove
invisible url encoded characters e.g.: "%0B"<br> WARNING:
maybe contains false-positives e.g. aa%0Baa -> aaaa.
</p>`

**Return:**
- `string <p>An clean UTF-8 encoded string.</p>`

--------

## cleanup(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Clean-up a string and show only printable UTF-8 chars at the end  + fix UTF-8 encoding.

EXAMPLE: <code>UTF8::cleanup("\xEF\xBB\xBF„Abcdef\xc2\xa0\x20…” — 😃 - DÃ¼sseldorf", true, true); // '„Abcdef  …” — 😃 - Düsseldorf'</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `string`

--------

## codepoints(string|string[] $arg, bool $use_u_style): int[]|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Accepts a string or an array of chars and returns an array of Unicode code points.

INFO: opposite to UTF8::string()

EXAMPLE: <code>
UTF8::codepoints('κöñ'); // array(954, 246, 241)
// ... OR ...
UTF8::codepoints('κöñ', true); // array('U+03ba', 'U+00f6', 'U+00f1')
</code>

**Parameters:**
- `T $arg <p>A UTF-8 encoded string or an array of such chars.</p>`
- `bool $use_u_style <p>If True, will return code points in U+xxxx format,
default, code points will be returned as integers.</p>`

**Return:**
- `int[]|string[] <p>
The array of code points:<br>
int[] for $u_style === false<br>
string[] for $u_style === true<br>
</p>`

--------

## collapseWhitespace(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Trims the string and replaces consecutive whitespace characters with a
single space. This includes tabs and newline characters, as well as
multibyte whitespace such as the thin space and ideographic space.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `string <p>A string with trimmed $str and condensed whitespace.</p>`

--------

## countChars(string $str, bool $clean_utf8, bool $try_to_use_mb_functions): int[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns count of characters used in a string.

EXAMPLE: <code>UTF8::countChars('κaκbκc'); // array('κ' => 3, 'a' => 1, 'b' => 1, 'c' => 1)</code>

**Parameters:**
- `T $str <p>The input string.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use`

**Return:**
- `int[] <p>An associative array of Character as keys and
their count as values.</p>`

--------

## cssIdentifier(string $str, string[] $filter, bool $strip_tags, bool $strtolower): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Create a valid CSS identifier for e.g. "class"- or "id"-attributes.

EXAMPLE: <code>UTF8::cssIdentifier('123foo/bar!!!'); // _23foo-bar</code>

copy&past from https://github.com/drupal/core/blob/8.8.x/lib/Drupal/Component/Utility/Html.php#L95

**Parameters:**
- `string $str <p>INFO: if no identifier is given e.g. " " or "", we will create a unique string automatically</p>`
- `array<string, string> $filter`
- `bool $strip_tags`
- `bool $strtolower`

**Return:**
- `string`

--------

## cssStripMediaQueries(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove css media-queries.

**Parameters:**
- `string $str`

**Return:**
- `string`

--------

## ctype_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether ctype is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## decimalToChr(int|string $int): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts an int value into a UTF-8 character.

INFO: opposite to UTF8::string()

EXAMPLE: <code>UTF8::decimalToChr(931); // 'Σ'</code>

**Parameters:**
- `int|string $int`

**Return:**
- `string`

--------

## decodeMimeHeader(string $str, string $encoding): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Decodes a MIME header field

**Parameters:**
- `string $str`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `false|string <p>A decoded MIME field on success,
or false if an error occurs during the decoding.</p>`

--------

## emojiDecode(string $str, bool $use_reversible_string_mappings): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Decodes a string which was encoded by "UTF8::emojiEncode()".

INFO: opposite to UTF8::emojiEncode()

EXAMPLE: <code>
UTF8::emojiDecode('foo CHARACTER_OGRE', false); // 'foo 👹'
//
UTF8::emojiDecode('foo _-_PORTABLE_UTF8_-_308095726_-_627590803_-_8FTU_ELBATROP_-_', true); // 'foo 👹'
</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $use_reversible_string_mappings [optional] <p>
When <b>TRUE</b>, we se a reversible string mapping
getSubstringBetween "emojiEncode" and "emojiDecode".</p>`

**Return:**
- `string`

--------

## emojiEncode(string $str, bool $use_reversible_string_mappings): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Encode a string with emoji chars into a non-emoji string.

INFO: opposite to UTF8::emojiDecode()

EXAMPLE: <code>
UTF8::emojiEncode('foo 👹', false)); // 'foo CHARACTER_OGRE'
//
UTF8::emojiEncode('foo 👹', true)); // 'foo _-_PORTABLE_UTF8_-_308095726_-_627590803_-_8FTU_ELBATROP_-_'
</code>

**Parameters:**
- `string $str <p>The input string</p>`
- `bool $use_reversible_string_mappings [optional] <p>
when <b>TRUE</b>, we use a reversible string mapping
getSubstringBetween "emojiEncode" and "emojiDecode"</p>`

**Return:**
- `string`

--------

## emojiFromCountryCode(string $country_code_iso_3166_1): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert any two-letter country code (ISO 3166-1) to the corresponding Emoji.

**Parameters:**
- `string $country_code_iso_3166_1 <p>e.g. DE</p>`

**Return:**
- `string <p>Emoji or empty string on error.</p>`

--------

## encode(string $to_encoding, string $str, bool $auto_detect_the_from_encoding, string $from_encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Encode a string with a new charset-encoding.

INFO:  This function will also try to fix broken / double encoding,
       so you can call this function also on a UTF-8 string and you don't mess up the string.

EXAMPLE: <code>
UTF8::encode('ISO-8859-1', '-ABC-中文空白-'); // '-ABC-????-'
//
UTF8::encode('UTF-8', '-ABC-中文空白-'); // '-ABC-中文空白-'
//
UTF8::encode('HTML', '-ABC-中文空白-'); // '-ABC-&#20013;&#25991;&#31354;&#30333;-'
//
UTF8::encode('BASE64', '-ABC-中文空白-'); // 'LUFCQy3kuK3mlofnqbrnmb0t'
</code>

**Parameters:**
- `string $to_encoding <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.</p>`
- `string $str <p>The input string</p>`
- `bool $auto_detect_the_from_encoding [optional] <p>Force the new encoding (we try to fix broken / double
encoding for UTF-8)<br> otherwise we auto-detect the current
string-encoding</p>`
- `string $from_encoding [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
A empty string will trigger the autodetect anyway.</p>`

**Return:**
- `string`

--------

## encodeMimeHeader(string $str, string $from_charset, string $to_charset, string $transfer_encoding, string $linefeed, int $indent): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `string $str`
- `string $from_charset [optional] <p>Set the input charset.</p>`
- `string $to_charset [optional] <p>Set the output charset.</p>`
- `string $transfer_encoding [optional] <p>Set the transfer encoding.</p>`
- `string $linefeed [optional] <p>Set the used linefeed.</p>`
- `int<1, max> $indent [optional] <p>Set the max length indent.</p>`

**Return:**
- `false|string <p>An encoded MIME field on success,
or false if an error occurs during the encoding.</p>`

--------

## extractText(string $str, string $search, int|null $length, string $replacer_for_skipped_text, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Create an extract from a sentence, so if the search-string was found, it tries to center in the output.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The searched string.</p>`
- `int|null $length [optional] <p>Default: null === text->length / 2</p>`
- `string $replacer_for_skipped_text [optional] <p>Default: …</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## file_get_contents(string $filename, bool $use_include_path, resource|null $context, int|null $offset, int|null $max_length, int $timeout, bool $convert_toUtf8, string $from_encoding): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Reads entire file into a string.

EXAMPLE: <code>UTF8::fileGetContents('utf16le.txt'); // ...</code>

WARNING: Do not use UTF-8 Option ($convert_toUtf8) for binary files (e.g.: images) !!!

**Parameters:**
- `string $filename <p>
Name of the file to read.
</p>`
- `bool $use_include_path [optional] <p>
Prior to PHP 5, this parameter is called
use_include_path and is a bool.
As of PHP 5 the FILE_USE_INCLUDE_PATH can be used
to trigger include path
search.
</p>`
- `resource|null $context [optional] <p>
A valid context resource created with
stream_context_create. If you don't need to use a
custom context, you can skip this parameter by &null;.
</p>`
- `int|null $offset [optional] <p>
The offset where the reading starts.
</p>`
- `int<0, max>|null $max_length [optional] <p>
Maximum length of data read. The default is to read until end
of file is reached.
</p>`
- `int $timeout <p>The time in seconds for the timeout.</p>`
- `bool $convert_toUtf8 <strong>WARNING!!!</strong> <p>Maybe you can't use this option for
some files, because they used non default utf-8 chars. Binary files
like images or pdf will not be converted.</p>`
- `string $from_encoding [optional] <p>e.g. 'UTF-16', 'UTF-8', 'ISO-8859-1', etc.<br>
A empty string will trigger the autodetect anyway.</p>`

**Return:**
- `false|string <p>The function returns the read data as string or <b>false</b> on failure.</p>`

--------

## fileHasBom(string $file_path): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks if a file starts with BOM (Byte Order Mark) character.

EXAMPLE: <code>UTF8::fileHasBom('utf8_with_bom.txt'); // true</code>

**Parameters:**
- `string $file_path <p>Path to a valid file.</p>`

**Return:**
- `bool <p><strong>true</strong> if the file has BOM at the start, <strong>false</strong> otherwise</p>`

--------

## filter(array|object|string $var, int $normalization_form, string $leading_combining): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

EXAMPLE: <code>UTF8::filter(array("\xE9", 'à', 'a')); // array('é', 'à', 'a')</code>

**Parameters:**
- `TFilter $var`
- `int $normalization_form`
- `string $leading_combining`

**Return:**
- `mixed`

--------

## filter_input(int $type, string $variable_name, int $filter, int|int[]|null $options): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
"filter_input()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

Gets a specific external variable by name and optionally filters it.

EXAMPLE: <code>
// _GET['foo'] = 'bar';
UTF8::filterInput(INPUT_GET, 'foo', FILTER_UNSAFE_RAW)); // 'bar'
</code>

**Parameters:**
- `int $type <p>
One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
<b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
<b>INPUT_ENV</b>.
</p>`
- `string $variable_name <p>
Name of a variable to get.
</p>`
- `int $filter [optional] <p>
The ID of the filter to apply. The
manual page lists the available filters.
</p>`
- `int|int[]|null $options [optional] <p>
Associative array of options or bitwise disjunction of flags. If filter
accepts options, flags can be provided in "flags" field of array.
</p>`

**Return:**
- `mixed <p>
Value of the requested variable on success, <b>FALSE</b> if the filter fails, or <b>NULL</b> if the
<i>variable_name</i> variable is not set. If the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it
returns <b>FALSE</b> if the variable is not set and <b>NULL</b> if the filter fails.
</p>`

--------

## filter_input_array(int $type, array|null $definition, bool $add_empty): array<string,mixed>|false|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
"filter_input_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

Gets external variables and optionally filters them.

EXAMPLE: <code>
// _GET['foo'] = 'bar';
UTF8::filterInputArray(INPUT_GET, array('foo' => 'FILTER_UNSAFE_RAW')); // array('bar')
</code>

**Parameters:**
- `int $type <p>
One of <b>INPUT_GET</b>, <b>INPUT_POST</b>,
<b>INPUT_COOKIE</b>, <b>INPUT_SERVER</b>, or
<b>INPUT_ENV</b>.
</p>`
- `array<string, mixed>|null $definition [optional] <p>
An array defining the arguments. A valid key is a string
containing a variable name and a valid value is either a filter type, or an array
optionally specifying the filter, flags and options. If the value is an
array, valid keys are filter which specifies the
filter type,
flags which specifies any flags that apply to the
filter, and options which specifies any options that
apply to the filter. See the example below for a better understanding.
</p>
<p>
This parameter can be also an integer holding a filter constant. Then all values in the
input array are filtered by this filter.
</p>`
- `bool $add_empty [optional] <p>
Add missing keys as <b>NULL</b> to the return value.
</p>`

**Return:**
- `array<string,mixed>|false|null <p>
An array containing the values of the requested variables on success, or <b>FALSE</b> on failure.
An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not
set. Or if the flag <b>FILTER_NULL_ON_FAILURE</b> is used, it returns <b>FALSE</b> if the variable
is not set and <b>NULL</b> if the filter fails.
</p>`

--------

## filter_var(float|int|string|null $variable, int $filter, int|int[] $options): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
"filter_var()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

Filters a variable with a specified filter.

EXAMPLE: <code>UTF8::filterVar('-ABC-中文空白-', FILTER_VALIDATE_URL); // false</code>

**Parameters:**
- `float|int|string|null $variable <p>
Value to filter.
</p>`
- `int $filter [optional] <p>
The ID of the filter to apply. The
manual page lists the available filters.
</p>`
- `int|int[] $options [optional] <p>
Associative array of options or bitwise disjunction of flags. If filter
accepts options, flags can be provided in "flags" field of array. For
the "callback" filter, callable type should be passed. The
callback must accept one argument, the value to be filtered, and return
the value after filtering/sanitizing it.
</p>
<p>
<code>
// for filters that accept options, use this format
$options = array(
'options' => array(
'default' => 3, // value to return if the filter fails
// other options here
'min_range' => 0
),
'flags' => FILTER_FLAG_ALLOW_OCTAL,
);
$var = filter_var('0755', FILTER_VALIDATE_INT, $options);
// for filter that only accept flags, you can pass them directly
$var = filter_var('oops', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
// for filter that only accept flags, you can also pass as an array
$var = filter_var('oops', FILTER_VALIDATE_BOOLEAN,
array('flags' => FILTER_NULL_ON_FAILURE));
// callback validate filter
function foo($value)
{
// Expected format: Surname, GivenNames
if (strpos($value, ", ") === false) return false;
list($surname, $givennames) = explode(", ", $value, 2);
$empty = (empty($surname) || empty($givennames));
$notstrings = (!is_string($surname) || !is_string($givennames));
if ($empty || $notstrings) {
return false;
} else {
return $value;
}
}
$var = filter_var('Doe, Jane Sue', FILTER_CALLBACK, array('options' => 'foo'));
</code>
</p>`

**Return:**
- `mixed <p>The filtered data, or <b>FALSE</b> if the filter fails.</p>`

--------

## filter_var_array(array $data, array|int $definition, bool $add_empty): array<string,mixed>|false|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
"filter_var_array()"-wrapper with normalizes to UTF-8 NFC, converting from WINDOWS-1252 when needed.

Gets multiple variables and optionally filters them.

EXAMPLE: <code>
$filters = [
    'name'  => ['filter'  => FILTER_CALLBACK, 'options' => [UTF8::class, 'ucwords']],
    'age'   => ['filter'  => FILTER_VALIDATE_INT, 'options' => ['min_range' => 1, 'max_range' => 120]],
    'email' => FILTER_VALIDATE_EMAIL,
];

$data = [
    'name' => 'κόσμε',
    'age' => '18',
    'email' => 'foo@bar.de'
];

UTF8::filterVarArray($data, $filters, true); // ['name' => 'Κόσμε', 'age' => 18, 'email' => 'foo@bar.de']
</code>

**Parameters:**
- `array<string, mixed> $data <p>
An array with string keys containing the data to filter.
</p>`
- `array<string, mixed>|int $definition [optional] <p>
An array defining the arguments. A valid key is a string
containing a variable name and a valid value is either a
filter type, or an
array optionally specifying the filter, flags and options.
If the value is an array, valid keys are filter
which specifies the filter type,
flags which specifies any flags that apply to the
filter, and options which specifies any options that
apply to the filter. See the example below for a better understanding.
</p>
<p>
This parameter can be also an integer holding a filter constant. Then all values
in the input array are filtered by this filter.
</p>`
- `bool $add_empty [optional] <p>
Add missing keys as <b>NULL</b> to the return value.
</p>`

**Return:**
- `array<string,mixed>|false|null <p>
An array containing the values of the requested variables on success, or <b>FALSE</b> on failure.
An array value will be <b>FALSE</b> if the filter fails, or <b>NULL</b> if the variable is not
set.
</p>`

--------

## finfo_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether finfo is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## firstChar(string $str, int $n, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the first $n characters of the string.

**Parameters:**
- `T $str <p>The input string.</p>`
- `int<1, max> $n <p>Number of characters to retrieve from the start.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## fitsInside(string $str, int $box_size): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the number of Unicode characters isn't greater than the specified integer.

EXAMPLE: <code>UTF8::fitsInside('κόσμε', 6); // false</code>

**Parameters:**
- `string $str the original string to be checked`
- `int $box_size the size in number of chars to be checked against string`

**Return:**
- `bool <p><strong>TRUE</strong> if string is less than or equal to $box_size, <strong>FALSE</strong> otherwise.</p>`

--------

## fixSimpleUtf8(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Try to fix simple broken UTF-8 strings.

INFO: Take a look at "UTF8::fixUtf8()" if you need a more advanced fix for broken UTF-8 strings.

EXAMPLE: <code>UTF8::fixSimpleUtf8('DÃ¼sseldorf'); // 'Düsseldorf'</code>

If you received an UTF-8 string that was converted from Windows-1252 as it was ISO-8859-1
(ignoring Windows-1252 chars from 80 to 9F) use this function to fix it.
See: http://en.wikipedia.org/wiki/Windows-1252

**Parameters:**
- `string $str <p>The input string</p>`

**Return:**
- `string`

--------

## fixUtf8(string|string[] $str): string|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Fix a double (or multiple) encoded UTF8 string.

EXAMPLE: <code>UTF8::fixUtf8('FÃÂÂÂÂ©dÃÂÂÂÂ©ration'); // 'Fédération'</code>

**Parameters:**
- `TFixUtf8 $str you can use a string or an array of strings`

**Return:**
- `string|string[] <p>Will return the fixed input-"array" or
the fixed input-"string".</p>`

--------

## getCharDirection(string $char): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get character of a specific character.

EXAMPLE: <code>UTF8::getCharDirection('ا'); // 'RTL'</code>

**Parameters:**
- `string $char`

**Return:**
- `string <p>'RTL' or 'LTR'.</p>`

--------

## getSupportInfo(string|null $key): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check for php-support.

**Parameters:**
- `string|null $key`

**Return:**
- `mixed Return the full support-"array", if $key === null<br>
return bool-value, if $key is used and available<br>
otherwise return <strong>null</strong>`

--------

## getUrlParamFromArray(string $param, array $data): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get data from an array via array like string.

EXAMPLE: <code>$array['foo'][123] = 'lall'; UTF8::getUrlParamFromArray('foo[123]', $array); // 'lall'</code>

**Parameters:**
- `string $param`
- `array<array-key, mixed> $data`

**Return:**
- `mixed`

--------

## getFileType(string $str, array $fallback): 
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Warning: this method only works for some file-types (png, jpg)
         if you need more supported types, please use e.g. "finfo"

**Parameters:**
- `string $str`
- `array{ext: (null|string), mime: (null|string), type: (null|string)} $fallback`

**Return:**
- `array{ext: (null|string), mime: (null|string), type: (null|string)}`

--------

## getRandomString(int $length, string $possible_chars, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `int<1, max> $length <p>Length of the random string.</p>`
- `T $possible_chars [optional] <p>Characters string for the random selection.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## getUniqueString(int|string $extra_entropy, bool $use_md5): non-empty-string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `int|string $extra_entropy [optional] <p>Extra entropy via a string or int value.</p>`
- `bool $use_md5 [optional] <p>Return the unique identifier as md5-hash? Default: true</p>`

**Return:**
- `non-empty-string`

--------

## hasLowercase(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains a lower case char, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not the string contains a lower case character.</p>`

--------

## hasUppercase(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains an upper case char, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not the string contains an upper case character.</p>`

--------

## has_whitespace(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains whitespace, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not the string contains whitespace.</p>`

--------

## hexToChr(string $hexdec): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts a hexadecimal value into a UTF-8 character.

INFO: opposite to UTF8::chrToHex()

EXAMPLE: <code>UTF8::hexToChr('U+00a7'); // '§'</code>

**Parameters:**
- `string $hexdec <p>The hexadecimal value.</p>`

**Return:**
- `string <p>One single UTF-8 character.</p>`

--------

## hexToInt(string $hexdec): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts hexadecimal U+xxxx code point representation to integer.

INFO: opposite to UTF8::intToHex()

EXAMPLE: <code>UTF8::hexToInt('U+00f1'); // 241</code>

**Parameters:**
- `string $hexdec <p>The hexadecimal code point representation.</p>`

**Return:**
- `false|int <p>The code point, or false on failure.</p>`

--------

## html_encode(string $str, bool $keep_ascii_chars, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts a UTF-8 string to a series of HTML numbered entities.

INFO: opposite to UTF8::html_decode()

EXAMPLE: <code>UTF8::htmlEncode('中文空白'); // '&#20013;&#25991;&#31354;&#30333;'</code>

**Parameters:**
- `T $str <p>The Unicode string to be encoded as numbered entities.</p>`
- `bool $keep_ascii_chars [optional] <p>Keep ASCII chars.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>HTML numbered entities.</p>`

--------

## html_entity_decode(string $str, int|null $flags, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
UTF-8 version of html_entity_decode()

The reason we are not using html_entity_decode() by itself is because
while it is not technically correct to leave out the semicolon
at the end of an entity most browsers will still interpret the entity
correctly. html_entity_decode() does not convert entities without
semicolons, so we are left with our own little solution here. Bummer.

Convert all HTML entities to their applicable characters.

INFO: opposite to UTF8::htmlEncode()

EXAMPLE: <code>UTF8htmlEntityDecode('&#20013;&#25991;&#31354;&#30333;'); // '中文空白'</code>

**Parameters:**
- `T $str <p>
The input string.
</p>`
- `int|null $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle quotes
and which document type to use. The default is ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The decoded string.</p>`

--------

## htmlEscape(string $str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Create a escape html version of the string via "UTF8::htmlspecialchars()".

**Parameters:**
- `string $str`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## htmlStripEmptyTags(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove empty html-tag.

e.g.: <pre><tag></tag></pre>

**Parameters:**
- `string $str`

**Return:**
- `string`

--------

## htmlentities(string $str, int $flags, string $encoding, bool $double_encode): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert all applicable characters to HTML entities: UTF-8 version of htmlentities().

EXAMPLE: <code>UTF8::htmlentities('<白-öäü>'); // '&lt;&#30333;-&ouml;&auml;&uuml;&gt;'</code>

**Parameters:**
- `string $str <p>
The input string.
</p>`
- `int $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle
quotes, invalid code unit sequences and the used document type. The default is
ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_IGNORE</b></td>
<td>
Silently discard invalid code unit sequences instead of returning
an empty string. Using this flag is discouraged as it
may have security implications.
</td>
</tr>
<tr valign="top">
<td><b>ENT_SUBSTITUTE</b></td>
<td>
Replace invalid code unit sequences with a Unicode Replacement Character
U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
string.
</td>
</tr>
<tr valign="top">
<td><b>ENT_DISALLOWED</b></td>
<td>
Replace invalid code points for the given document type with a
Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
(otherwise) instead of leaving them as is. This may be useful, for
instance, to ensure the well-formedness of XML documents with
embedded external content.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>`
- `string $encoding [optional] <p>
Like <b>htmlspecialchars</b>,
<b>htmlentities</b> takes an optional third argument
<i>encoding</i> which defines encoding used in
conversion.
Although this argument is technically optional, you are highly
encouraged to specify the correct value for your code.
</p>`
- `bool $double_encode [optional] <p>
When <i>double_encode</i> is turned off PHP will not
encode existing html entities. The default is to convert everything.
</p>`

**Return:**
- `string <p>
The encoded string.
<br><br>
If the input <i>string</i> contains an invalid code unit
sequence within the given <i>encoding</i> an empty string
will be returned, unless either the <b>ENT_IGNORE</b> or
<b>ENT_SUBSTITUTE</b> flags are set.
</p>`

--------

## htmlspecialchars(string $str, int $flags, string $encoding, bool $double_encode): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert only special characters to HTML entities: UTF-8 version of htmlspecialchars()

INFO: Take a look at "UTF8::htmlentities()"

EXAMPLE: <code>UTF8::htmlspecialchars('<白-öäü>'); // '&lt;白-öäü&gt;'</code>

**Parameters:**
- `T $str <p>
The string being converted.
</p>`
- `int $flags [optional] <p>
A bitmask of one or more of the following flags, which specify how to handle
quotes, invalid code unit sequences and the used document type. The default is
ENT_COMPAT | ENT_HTML401.
<table>
Available <i>flags</i> constants
<tr valign="top">
<td>Constant Name</td>
<td>Description</td>
</tr>
<tr valign="top">
<td><b>ENT_COMPAT</b></td>
<td>Will convert double-quotes and leave single-quotes alone.</td>
</tr>
<tr valign="top">
<td><b>ENT_QUOTES</b></td>
<td>Will convert both double and single quotes.</td>
</tr>
<tr valign="top">
<td><b>ENT_NOQUOTES</b></td>
<td>Will leave both double and single quotes unconverted.</td>
</tr>
<tr valign="top">
<td><b>ENT_IGNORE</b></td>
<td>
Silently discard invalid code unit sequences instead of returning
an empty string. Using this flag is discouraged as it
may have security implications.
</td>
</tr>
<tr valign="top">
<td><b>ENT_SUBSTITUTE</b></td>
<td>
Replace invalid code unit sequences with a Unicode Replacement Character
U+FFFD (UTF-8) or &#38;#38;#FFFD; (otherwise) instead of returning an empty
string.
</td>
</tr>
<tr valign="top">
<td><b>ENT_DISALLOWED</b></td>
<td>
Replace invalid code points for the given document type with a
Unicode Replacement Character U+FFFD (UTF-8) or &#38;#38;#FFFD;
(otherwise) instead of leaving them as is. This may be useful, for
instance, to ensure the well-formedness of XML documents with
embedded external content.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML401</b></td>
<td>
Handle code as HTML 4.01.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XML1</b></td>
<td>
Handle code as XML 1.
</td>
</tr>
<tr valign="top">
<td><b>ENT_XHTML</b></td>
<td>
Handle code as XHTML.
</td>
</tr>
<tr valign="top">
<td><b>ENT_HTML5</b></td>
<td>
Handle code as HTML 5.
</td>
</tr>
</table>
</p>`
- `string $encoding [optional] <p>
Defines encoding used in conversion.
</p>
<p>
For the purposes of this function, the encodings
ISO-8859-1, ISO-8859-15,
UTF-8, cp866,
cp1251, cp1252, and
KOI8-R are effectively equivalent, provided the
<i>string</i> itself is valid for the encoding, as
the characters affected by <b>htmlspecialchars</b> occupy
the same positions in all of these encodings.
</p>`
- `bool $double_encode [optional] <p>
When <i>double_encode</i> is turned off PHP will not
encode existing html entities, the default is to convert everything.
</p>`

**Return:**
- `string <p>The converted string.</p>
<p>
If the input <i>string</i> contains an invalid code unit
sequence within the given <i>encoding</i> an empty string
will be returned, unless either the <b>ENT_IGNORE</b> or
<b>ENT_SUBSTITUTE</b> flags are set.</p>`

--------

## iconv_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether iconv is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## intToHex(int $int, string $prefix): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts Integer to hexadecimal U+xxxx code point representation.

INFO: opposite to UTF8::hexToInt()

EXAMPLE: <code>UTF8::intToHex(241); // 'U+00f1'</code>

**Parameters:**
- `int $int <p>The integer to be converted to hexadecimal code point.</p>`
- `string $prefix [optional]`

**Return:**
- `string the code point, or empty string on failure`

--------

## intlChar_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether intl-char is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## intl_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether intl is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## isAlpha(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only alphabetic chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only alphabetic chars.</p>`

--------

## isAlphanumeric
(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only alphabetic and numeric chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only alphanumeric chars.</p>`

--------

## isAscii(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks if a string is 7 bit ASCII.

EXAMPLE: <code>UTF8::isAscii('白'); // false</code>

**Parameters:**
- `string $str <p>The string to check.</p>`

**Return:**
- `bool <p>
<strong>true</strong> if it is ASCII<br>
<strong>false</strong> otherwise
</p>`

--------

## isBase64(string|null $str, bool $empty_string_is_valid): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string is base64 encoded, false otherwise.

EXAMPLE: <code>UTF8::isBase64('4KSu4KWL4KSo4KS/4KSa'); // true</code>

**Parameters:**
- `string|null $str <p>The input string.</p>`
- `bool $empty_string_is_valid [optional] <p>Is an empty string valid base64 or not?</p>`

**Return:**
- `bool <p>Whether or not $str is base64 encoded.</p>`

--------

## is_binary(int|string $input, bool $strict): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the input is binary... (is look like a hack).

EXAMPLE: <code>UTF8::isBinary(01); // true</code>

**Parameters:**
- `int|string $input`
- `bool $strict`

**Return:**
- `bool`

--------

## isBinaryFile(string $file): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the file is binary.

EXAMPLE: <code>UTF8::isBinary('./utf32.txt'); // true</code>

**Parameters:**
- `string $file`

**Return:**
- `bool`

--------

## isBlank(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only whitespace chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only whitespace characters.</p>`

--------

## isBom(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks if the given string is equal to any "Byte Order Mark".

WARNING: Use "UTF8::hasBom()" if you will check BOM in a string.

EXAMPLE: <code>UTF8::isBom("\xef\xbb\xbf"); // true</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p><strong>true</strong> if the $utf8_chr is Byte Order Mark, <strong>false</strong> otherwise.</p>`

--------

## isEmpty(array|float|int|string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Determine whether the string is considered to be empty.

A variable is considered empty if it does not exist or if its value equals FALSE.
empty() does not generate a warning if the variable does not exist.

**Parameters:**
- `array<array-key, mixed>|float|int|string $str`

**Return:**
- `bool <p>Whether or not $str is empty().</p>`

--------

## isHexadecimal(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only hexadecimal chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only hexadecimal chars.</p>`

--------

## isHtml(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string contains any HTML tags.

EXAMPLE: <code>UTF8::isHtml('<b>lall</b>'); // true</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains html elements.</p>`

--------

## is_json(string $str, bool $only_array_or_object_results_are_valid): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Try to check if "$str" is a JSON-string.

EXAMPLE: <code>UTF8::is_json('{"array":[1,"¥","ä"]}'); // true</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $only_array_or_object_results_are_valid [optional] <p>Only array and objects are valid json
results.</p>`

**Return:**
- `bool <p>Whether or not the $str is in JSON format.</p>`

--------

## isLowercase(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only lowercase chars.</p>`

--------

## isPrintable(string $str, bool $ignore_control_characters): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only printable (non-invisible) chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $ignore_control_characters [optional] <p>Ignore control characters like [LRM] or [LSEP].</p>`

**Return:**
- `bool <p>Whether or not $str contains only printable (non-invisible) chars.</p>`

--------

## isPunctuation(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only punctuation chars, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only punctuation chars.</p>`

--------

## isSerialized(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string is serialized, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str is serialized.</p>`

--------

## isUppercase(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains only lower case chars, false
otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>Whether or not $str contains only lower case characters.</p>`

--------

## isUrl(string $url, bool $disallow_localhost): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if $url is an correct url.

**Parameters:**
- `string $url`
- `bool $disallow_localhost`

**Return:**
- `bool`

--------

## isUtf8(int|string|string[]|null $str, bool $strict): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether the passed input contains only byte sequences that appear valid UTF-8.

EXAMPLE: <code>
UTF8::isUtf8(['Iñtërnâtiônàlizætiøn', 'foo']); // true
//
UTF8::isUtf8(["Iñtërnâtiônàlizætiøn\xA0\xA1", 'bar']); // false
</code>

**Parameters:**
- `int|string|string[]|null $str <p>The input to be checked.</p>`
- `bool $strict <p>Check also if the string is not UTF-16 or UTF-32.</p>`

**Return:**
- `bool`

--------

## isUtf16(string $str, bool $check_if_string_is_binary): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string is UTF-16.

EXAMPLE: <code>
UTF8::isUtf16(file_get_contents('utf-16-le.txt')); // 1
//
UTF8::isUtf16(file_get_contents('utf-16-be.txt')); // 2
//
UTF8::isUtf16(file_get_contents('utf-8.txt')); // false
</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $check_if_string_is_binary`

**Return:**
- `false|int <strong>false</strong> if is't not UTF-16,<br>
<strong>1</strong> for UTF-16LE,<br>
<strong>2</strong> for UTF-16BE`

--------

##isUtf32(string $str, bool $check_if_string_is_binary): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string is UTF-32.

EXAMPLE: <code>
UTF8::isUtf32(file_get_contents('utf-32-le.txt')); // 1
//
UTF8::isUtf32(file_get_contents('utf-32-be.txt')); // 2
//
UTF8::isUtf32(file_get_contents('utf-8.txt')); // false
</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $check_if_string_is_binary`

**Return:**
- `false|int <strong>false</strong> if is't not UTF-32,<br>
<strong>1</strong> for UTF-32LE,<br>
<strong>2</strong> for UTF-32BE`

--------

## jsonDecode(string $json, bool $assoc, int $depth, int $options): mixed
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
(PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
Decodes a JSON string

EXAMPLE: <code>UTF8::jsonDecode('[1,"\u00a5","\u00e4"]'); // array(1, '¥', 'ä')</code>

**Parameters:**
- `string $json <p>
The <i>json</i> string being decoded.
</p>
<p>
This function only works with UTF-8 encoded strings.
</p>
<p>PHP implements a superset of
JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
only supports these values when they are nested inside an array or an object.
</p>`
- `bool $assoc [optional] <p>
When <b>TRUE</b>, returned objects will be converted into
associative arrays.
</p>`
- `int $depth [optional] <p>
User specified recursion depth.
</p>`
- `int $options [optional] <p>
Bitmask of JSON decode options. Currently only
<b>JSON_BIGINT_AS_STRING</b>
is supported (default is to cast large integers as floats)
</p>`

**Return:**
- `mixed <p>The value encoded in <i>json</i> in appropriate PHP type. Values true, false and
null (case-insensitive) are returned as <b>TRUE</b>, <b>FALSE</b> and <b>NULL</b> respectively.
<b>NULL</b> is returned if the <i>json</i> cannot be decoded or if the encoded data
is deeper than the recursion limit.</p>`

--------

## json_encode(mixed $value, int $options, int $depth): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
(PHP 5 &gt;= 5.2.0, PECL json &gt;= 1.2.0)<br/>
Returns the JSON representation of a value.

EXAMPLE: <code>UTF8::jsonEncode(array(1, '¥', 'ä')); // '[1,"\u00a5","\u00e4"]'</code>

**Parameters:**
- `mixed $value <p>
The <i>value</i> being encoded. Can be any type except
a resource.
</p>
<p>
All string data must be UTF-8 encoded.
</p>
<p>PHP implements a superset of
JSON - it will also encode and decode scalar types and <b>NULL</b>. The JSON standard
only supports these values when they are nested inside an array or an object.
</p>`
- `int $options [optional] <p>
Bitmask consisting of <b>JSON_HEX_QUOT</b>,
<b>JSON_HEX_TAG</b>,
<b>JSON_HEX_AMP</b>,
<b>JSON_HEX_APOS</b>,
<b>JSON_NUMERIC_CHECK</b>,
<b>JSON_PRETTY_PRINT</b>,
<b>JSON_UNESCAPED_SLASHES</b>,
<b>JSON_FORCE_OBJECT</b>,
<b>JSON_UNESCAPED_UNICODE</b>. The behaviour of these
constants is described on
the JSON constants page.
</p>`
- `int $depth [optional] <p>
Set the maximum depth. Must be greater than zero.
</p>`

**Return:**
- `false|string <p>A JSON encoded <strong>string</strong> on success or<br>
<strong>FALSE</strong> on failure.</p>`

--------

## json_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether JSON is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## lcfirst(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Makes string's first char lowercase.

EXAMPLE: <code>UTF8::lcfirst('ÑTËRNÂTIÔNÀLIZÆTIØN'); // ñTËRNÂTIÔNÀLIZÆTIØN</code>

**Parameters:**
- `string $str <p>The input string</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>The resulting string.</p>`

--------

## lcwords(string $str, string[] $exceptions, string $char_list, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Lowercase for all words in the string.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string[] $exceptions [optional] <p>Exclusion for some words.</p>`
- `string $char_list [optional] <p>Additional chars that contains to words and do
not start a new word.</p>`
- `string $encoding [optional] <p>Set the charset.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string`

--------

## levenshtein(string $str1, string $str2, int $insertionCost, int $replacementCost, int $deletionCost): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Calculate Levenshtein distance getSubstringBetween two strings.

For better performance, in a real application with a single input string
matched against many strings from a database, you will probably want to pre-
encode the input only once and use \levenshtein().

Source: https://github.com/KEINOS/mb_levenshtein

**Parameters:**
- `string $str1 <p>One of the strings being evaluated for Levenshtein distance.</p>`
- `string $str2 <p>One of the strings being evaluated for Levenshtein distance.</p>`
- `int $insertionCost [optional] <p>Defines the cost of insertion.</p>`
- `int $replacementCost [optional] <p>Defines the cost of replacement.</p>`
- `int $deletionCost [optional] <p>Defines the cost of deletion.</p>`

**Return:**
- `int`

--------

## ltrim(string $str, string|null $chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Strip whitespace or other characters from the beginning of a UTF-8 string.

EXAMPLE: <code>UTF8::ltrim('　中文空白　 '); // '中文空白　 '</code>

**Parameters:**
- `string $str <p>The string to be trimmed</p>`
- `string|null $chars <p>Optional characters to be stripped</p>`

**Return:**
- `string the string with unwanted characters stripped from the left`

--------

## max(string|string[] $arg): string|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the UTF-8 character with the maximum code point in the given data.

EXAMPLE: <code>UTF8::max('abc-äöü-中文空白'); // 'ø'</code>

**Parameters:**
- `string|string[] $arg <p>A UTF-8 encoded string or an array of such strings.</p>`

**Return:**
- `string|null the character with the highest code point than others, returns null on failure or empty input`

--------

## maxChrWidth(string $str): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Calculates and returns the maximum number of bytes taken by any
UTF-8 encoded character in the given string.

EXAMPLE: <code>UTF8::maxChrWidth('Intërnâtiônàlizætiøn'); // 2</code>

**Parameters:**
- `string $str <p>The original Unicode string.</p>`

**Return:**
- `int <p>Max byte lengths of the given chars.</p>`

--------

## mbstring_loaded(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether mbstring is available on the server.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if available, <strong>false</strong> otherwise</p>`

--------

## min(string|string[] $arg): string|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the UTF-8 character with the minimum code point in the given data.

EXAMPLE: <code>UTF8::min('abc-äöü-中文空白'); // '-'</code>

**Parameters:**
- `string|string[] $arg <strong>A UTF-8 encoded string or an array of such strings.</strong>`

**Return:**
- `string|null <p>The character with the lowest code point than others, returns null on failure or empty input.</p>`

--------

## normalizeEncoding(mixed $encoding, mixed $fallback): mixed|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Normalize the encoding-"name" input.

EXAMPLE: <code>UTF8::normalizeEncoding('UTF8'); // 'UTF-8'</code>

**Parameters:**
- `mixed $encoding <p>e.g.: ISO, UTF8, WINDOWS-1251 etc.</p>`
- `string|TNormalizeEncodingFallback $fallback <p>e.g.: UTF-8</p>`

**Return:**
- `mixed|string <p>e.g.: ISO-8859-1, UTF-8, WINDOWS-1251 etc.<br>Will return a empty string as fallback (by default)</p>`

--------

## normalizeLineEnding(string $str, string|string[] $replacer): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Standardize line ending to unix-like.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string|string[] $replacer <p>The replacer char e.g. "\n" (Linux) or "\r\n" (Windows). You can also use \PHP_EOL
here.</p>`

**Return:**
- `string <p>A string with normalized line ending.</p>`

--------

## normalizeMsWord(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Normalize some MS Word special characters.

EXAMPLE: <code>UTF8::normalizeMsWord('„Abcdef…”'); // '"Abcdef..."'</code>

**Parameters:**
- `string $str <p>The string to be normalized.</p>`

**Return:**
- `string <p>A string with normalized characters for commonly used chars in Word documents.</p>`

--------

## normalizeWhitespace(string $str, bool $keep_non_breaking_space, bool $keep_bidi_unicode_controls, bool $normalize_control_characters): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Normalize the whitespace.

EXAMPLE: <code>UTF8::normalizeWhitespace("abc-\xc2\xa0-öäü-\xe2\x80\xaf-\xE2\x80\xAC", true); // "abc-\xc2\xa0-öäü- -"</code>

**Parameters:**
- `string $str <p>The string to be normalized.</p>`
- `bool $keep_non_breaking_space [optional] <p>Set to true, to keep non-breaking-spaces.</p>`
- `bool $keep_bidi_unicode_controls [optional] <p>Set to true, to keep non-printable (for the web)
bidirectional text chars.</p>`
- `bool $normalize_control_characters [optional] <p>Set to true, to convert e.g. LINE-, PARAGRAPH-SEPARATOR with "\n" and LINE TABULATION with "\t".</p>`

**Return:**
- `string <p>A string with normalized whitespace.</p>`

--------

## ord(string $chr, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Calculates Unicode code point of the given UTF-8 encoded character.

INFO: opposite to UTF8::chr()

EXAMPLE: <code>UTF8::ord('☃'); // 0x2603</code>

**Parameters:**
- `string $chr <p>The character of which to calculate code point.<p/>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <p>Unicode code point of the given character,<br>
0 on invalid UTF-8 byte sequence</p>`

--------

## parse_str(string $str, array $result, bool $clean_utf8): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Parses the string into an array (into the the second parameter).

WARNING: Unlike "parse_str()", this method does not (re-)place variables in the current scope,
         if the second parameter is not set!

EXAMPLE: <code>
UTF8::parseStr('Iñtërnâtiônéàlizætiøn=測試&arr[]=foo+測試&arr[]=ການທົດສອບ', $array);
echo $array['Iñtërnâtiônéàlizætiøn']; // '測試'
</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `array<string, mixed> $result <p>The result will be returned into this reference parameter.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `bool <p>Will return <strong>false</strong> if php can't parse the string and we haven't any $result.</p>`

--------

## pcre_utf8_support(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks if \u modifier is available that enables Unicode support in PCRE.

**Parameters:**
__nothing__

**Return:**
- `bool <p>
<strong>true</strong> if support is available,<br>
<strong>false</strong> otherwise
</p>`

--------

## range(int|string $var1, int|string $var2, bool $use_ctype, string $encoding, float|int $step): list<string>
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Create an array containing a range of UTF-8 characters.

EXAMPLE: <code>UTF8::range('κ', 'ζ'); // array('κ', 'ι', 'θ', 'η', 'ζ',)</code>

**Parameters:**
- `int|string $var1 <p>Numeric or hexadecimal code points, or a UTF-8 character to start from.</p>`
- `int|string $var2 <p>Numeric or hexadecimal code points, or a UTF-8 character to end at.</p>`
- `bool $use_ctype <p>use ctype to detect numeric and hexadecimal, otherwise we will use a simple
"is_numeric"</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `float|int $step [optional] <p>
If a step value is given, it will be used as the
increment getSubstringBetween elements in the sequence. step
should be given as a positive number. If not specified,
step will default to 1.
</p>`

**Return:**
- `list<string>`

--------

## rawurldecode(string $str, bool $multi_decode): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Multi decode HTML entity + fix urlencoded-win1252-chars.

EXAMPLE: <code>UTF8::rawurldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest+test'</code>

e.g:
'test+test'                     => 'test+test'
'D&#252;sseldorf'               => 'Düsseldorf'
'D%FCsseldorf'                  => 'Düsseldorf'
'D&#xFC;sseldorf'               => 'Düsseldorf'
'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
'DÃ¼sseldorf'                   => 'Düsseldorf'
'D%C3%BCsseldorf'               => 'Düsseldorf'
'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'

**Parameters:**
- `T $str <p>The input string.</p>`
- `bool $multi_decode <p>Decode as often as possible.</p>`

**Return:**
- `string <p>The decoded URL, as a string.</p>`

--------

## regexReplace(string $str, string $pattern, string $replacement, string $options, string $delimiter): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces all occurrences of $pattern in $str by $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $pattern <p>The regular expression pattern.</p>`
- `string $replacement <p>The string to replace with.</p>`
- `string $options [optional] <p>Matching conditions to be used.</p>`
- `string $delimiter [optional] <p>Delimiter the the regex. Default: '/'</p>`

**Return:**
- `string`

--------

## removeBom(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove the BOM from UTF-8 / UTF-16 / UTF-32 strings.

EXAMPLE: <code>UTF8::removeBom("\xEF\xBB\xBFΜπορώ να"); // 'Μπορώ να'</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `string <p>A string without UTF-BOM.</p>`

--------

## remove_duplicates(string $str, string|string[] $what): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Removes duplicate occurrences of a string in another string.

EXAMPLE: <code>UTF8::remove_duplicates('öäü-κόσμεκόσμε-äöü', 'κόσμε'); // 'öäü-κόσμε-äöü'</code>

**Parameters:**
- `string $str <p>The base string.</p>`
- `string|string[] $what <p>String to search for in the base string.</p>`

**Return:**
- `string <p>A string with removed duplicates.</p>`

--------

## remove_html(string $str, string $allowable_tags): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove html via "strip_tags()" from the string.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $allowable_tags [optional] <p>You can use the optional second parameter to specify tags which
should not be stripped. Default: null
</p>`

**Return:**
- `string <p>A string with without html tags.</p>`

--------

## remove_html_breaks(string $str, string $replacement): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove all breaks [<br> | \r\n | \r | \n | ...] from the string.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $replacement [optional] <p>Default is a empty string.</p>`

**Return:**
- `string <p>A string without breaks.</p>`

--------

## remove_ileft(string $str, string $substring, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string with the prefix $substring removed, if present and case-insensitive.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $substring <p>The prefix to remove.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>A string without the prefix $substring.</p>`

--------

## removeInvisibleCharacters(string $str, bool $url_encoded, string $replacement, bool $keep_basic_control_characters): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Remove invisible characters from a string.

e.g.: This prevents sandwiching null characters getSubstringBetween ascii characters, like Java\0script.

EXAMPLE: <code>UTF8::removeInvisibleCharacters("κόσ\0με"); // 'κόσμε'</code>

copy&past from https://github.com/bcit-ci/CodeIgniter/blob/develop/system/core/Common.php

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $url_encoded [optional] <p>
Try to remove url encoded control character.
WARNING: maybe contains false-positives e.g. aa%0Baa -> aaaa.
<br>
Default: false
</p>`
- `string $replacement [optional] <p>The replacement character.</p>`
- `bool $keep_basic_control_characters [optional] <p>Keep control characters like [LRM] or [LSEP].</p>`

**Return:**
- `string <p>A string without invisible chars.</p>`

--------

## remove_iright(string $str, string $substring, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string with the suffix $substring removed, if present and case-insensitive.

**Parameters:**
- `string $str`
- `string $substring <p>The suffix to remove.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>A string having a $str without the suffix $substring.</p>`

--------

## remove_left(string $str, string $substring, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string with the prefix $substring removed, if present.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $substring <p>The prefix to remove.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>A string without the prefix $substring.</p>`

--------

## remove_right(string $str, string $substring, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string with the suffix $substring removed, if present.

**Parameters:**
- `string $str`
- `string $substring <p>The suffix to remove.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>A string having a $str without the suffix $substring.</p>`

--------

## replace(string $str, string $search, string $replacement, bool $case_sensitive): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces all occurrences of $search in $str by $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The needle to search for.</p>`
- `string $replacement <p>The string to replace with.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`

**Return:**
- `string <p>A string with replaced parts.</p>`

--------

## replace_all(string $str, string[] $search, string|string[] $replacement, bool $case_sensitive): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces all occurrences of $search in $str by $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string[] $search <p>The elements to search for.</p>`
- `string|string[] $replacement <p>The string to replace with.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`

**Return:**
- `string <p>A string with replaced parts.</p>`

--------

## replaceDiamondQuestionMark(string $str, string $replacement_char, bool $process_invalid_utf8_chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replace the diamond question mark (�) and invalid-UTF8 chars with the replacement.

EXAMPLE: <code>UTF8::replaceDiamondQuestionMark('中文空白�', ''); // '中文空白'</code>

**Parameters:**
- `string $str <p>The input string</p>`
- `string $replacement_char <p>The replacement character.</p>`
- `bool $process_invalid_utf8_chars <p>Convert invalid UTF-8 chars </p>`

**Return:**
- `string <p>A string without diamond question marks (�).</p>`

--------

## rtrim(string $str, string|null $chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Strip whitespace or other characters from the end of a UTF-8 string.

EXAMPLE: <code>UTF8::rtrim('-ABC-中文空白-  '); // '-ABC-中文空白-'</code>

**Parameters:**
- `string $str <p>The string to be trimmed.</p>`
- `string|null $chars <p>Optional characters to be stripped.</p>`

**Return:**
- `string <p>A string with unwanted characters stripped from the right.</p>`

--------

## showSupport(bool $useEcho): string|void
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
WARNING: Print native UTF-8 support (libs) by default, e.g. for debugging.

**Parameters:**
- `bool $useEcho`

**Return:**
- `string|void`

--------

## single_chr_html_encode(string $char, bool $keep_ascii_chars, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts a UTF-8 character to HTML Numbered Entity like "&#123;".

EXAMPLE: <code>UTF8::singleChrHtmlEncode('κ'); // '&#954;'</code>

**Parameters:**
- `T $char <p>The Unicode character to be encoded as numbered entity.</p>`
- `bool $keep_ascii_chars <p>Set to <strong>true</strong> to keep ASCII chars.</>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The HTML numbered entity for the given character.</p>`

--------

## spaces_to_tabs(string $str, int $tab_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `T $str`
- `int<1, max> $tab_length`

**Return:**
- `string`

--------

## str_camelize(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a camelCase version of the string. Trims surrounding spaces,
capitalizes letters following digits, spaces, dashes and underscores,
and removes spaces, dashes, as well as underscores.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string`

--------

## str_capitalize_name(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the string with the first letter of each word capitalized,
except for when the word is a name which shouldn't be capitalized.

**Parameters:**
- `string $str`

**Return:**
- `string <p>A string with $str capitalized.</p>`

--------

## strContains(string $haystack, string $needle, bool $case_sensitive): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains $needle, false otherwise. By default
the comparison is case-sensitive, but can be made insensitive by setting
$case_sensitive to false.

**Parameters:**
- `string $haystack <p>The input string.</p>`
- `string $needle <p>Substring to look for.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`

**Return:**
- `bool <p>Whether or not $haystack contains $needle.</p>`

--------

## strContainsAll(string $haystack, scalar[] $needles, bool $case_sensitive): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains all $needles, false otherwise. By
default, the comparison is case-sensitive, but can be made insensitive by
setting $case_sensitive to false.

**Parameters:**
- `string $haystack <p>The input string.</p>`
- `scalar[] $needles <p>SubStrings to look for.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`

**Return:**
- `bool <p>Whether or not $haystack contains $needle.</p>`

--------

## strContainsAny(string $haystack, scalar[] $needles, bool $case_sensitive): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string contains any $needles, false otherwise. By
default the comparison is case-sensitive, but can be made insensitive by
setting $case_sensitive to false.

**Parameters:**
- `string $haystack <p>The input string.</p>`
- `scalar[] $needles <p>SubStrings to look for.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`

**Return:**
- `bool <p>Whether or not $str contains $needle.</p>`

--------

## strDelimit(string $str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a lowercase and trimmed string separated by dashes. Dashes are
inserted before uppercase characters (with the exception of the first
character of the string), and in place of spaces as well as underscores.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strDelimit(string $str, string $delimiter, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a lowercase and trimmed string separated by the given delimiter.

Delimiters are inserted before uppercase characters (with the exception
of the first character of the string), and in place of spaces, dashes,
and underscores. Alpha delimiters are not converted to lowercase.

EXAMPLE: <code>
UTF8::strDelimit('test case, '#'); // 'test#case'
UTF8::strDelimit('test -case', '**'); // 'test**case'
</code>

**Parameters:**
- `T $str <p>The input string.</p>`
- `string $delimiter <p>Sequence used to separate parts of the string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ ->
ß</p>`

**Return:**
- `string`

--------

## detectStringEncoding(string $str): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Optimized "mb_detect_encoding()"-function -> with support for UTF-16 and UTF-32.

EXAMPLE: <code>
UTF8::detectStringEncoding('中文空白'); // 'UTF-8'
UTF8::detectStringEncoding('Abc'); // 'ASCII'
</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `false|string <p>
The detected string-encoding e.g. UTF-8 or UTF-16BE,<br>
otherwise it will return false e.g. for BINARY or not detected encoding.
</p>`

--------

## str_ends_with(string $haystack, string $needle): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string ends with the given substring.

EXAMPLE: <code>
UTF8strEndsWith('BeginMiddleΚόσμε', 'Κόσμε'); // true
UTF8strEndsWith('BeginMiddleΚόσμε', 'κόσμε'); // false
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `bool`

--------

## strEndsWithAny(string $str, string[] $substrings): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string ends with any of $substrings, false otherwise.

- case-sensitive

**Parameters:**
- `string $str <p>The input string.</p>`
- `string[] $substrings <p>Substrings to look for.</p>`

**Return:**
- `bool <p>Whether or not $str ends with $substring.</p>`

--------

## strEnsureLeft(string $str, string $substring): 
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Ensures that the string begins with $substring. If it doesn't, it's
prepended.

**Parameters:**
- `T $str <p>The input string.</p>`
- `TSub $substring <p>The substring to add if not present.</p>`

**Return:**
- `TSub is non-empty-string ? non-empty-string : (T is non-empty-string ? non-empty-string : string`

--------

## strEnsureRight(string $str, string $substring): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Ensures that the string ends with $substring. If it doesn't, it's appended.

**Parameters:**
- `T $str <p>The input string.</p>`
- `TSub $substring <p>The substring to add if not present.</p>`

**Return:**
- `string`

--------

## strHumanize(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Capitalizes the first word of the string, replaces underscores with
spaces, and strips '_id'.

**Parameters:**
- `string $str`

**Return:**
- `string`

--------

## strEndsWithInsensitive(string $haystack, string $needle): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string ends with the given substring, case-insensitive.

EXAMPLE: <code>
UTF8::strEndsWithInsensitive('BeginMiddleΚόσμε', 'Κόσμε'); // true
UTF8::strEndsWithInsensitive('BeginMiddleΚόσμε', 'κόσμε'); // true
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `bool`

--------

## strEndsWithAnyInsensitive(string $str, string[] $substrings): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string ends with any of $substrings, false otherwise.

- case-insensitive

**Parameters:**
- `string $str <p>The input string.</p>`
- `string[] $substrings <p>Substrings to look for.</p>`

**Return:**
- `bool <p>Whether or not $str ends with $substring.</p>`

--------

## strInsert(string $str, string $substring, int $index, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Inserts $substring into the string at the $index provided.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $substring <p>String to be inserted.</p>`
- `int $index <p>The index at which to insert the substring.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strReplaceInsensitive(string|string[] $search, string|string[] $replacement, string|string[] $subject, int $count): string|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Case-insensitive and UTF-8 safe version of <function>str_replace</function>.

EXAMPLE: <code>
UTF8::strReplaceInsensitive('lIzÆ', 'lise', 'Iñtërnâtiônàlizætiøn'); // 'Iñtërnâtiônàlisetiøn'
</code>

**Parameters:**
- `string|string[] $search <p>
Every replacement with search array is
performed on the result of previous replacement.
</p>`
- `string|string[] $replacement <p>The replacement.</p>`
- `TStrIReplaceSubject $subject <p>
If subject is an array, then the search and
replace is performed with every entry of
subject, and the return value is an array as
well.
</p>`
- `int $count [optional] <p>
The number of matched and replaced needles will
be returned in count which is passed by
reference.
</p>`

**Return:**
- `string|string[] <p>A string or an array of replacements.</p>`

--------

## strReplaceInsensitive_beginning(string $str, string $search, string $replacement): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces $search from the beginning of string with $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The string to search for.</p>`
- `string $replacement <p>The replacement.</p>`

**Return:**
- `string <p>The string after the replacement.</p>`

--------

## strReplaceEndingInsensitive(string $str, string $search, string $replacement): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces $search from the ending of string with $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The string to search for.</p>`
- `string $replacement <p>The replacement.</p>`

**Return:**
- `string <p>The string after the replacement.</p>`

--------

## strStartsWithInsensitive(string $haystack, string $needle): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string starts with the given substring, case-insensitive.

EXAMPLE: <code>
UTF8::strStartsWithInsensitive('ΚόσμεMiddleEnd', 'Κόσμε'); // true
UTF8::strStartsWithInsensitive('ΚόσμεMiddleEnd', 'κόσμε'); // true
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `bool`

--------

## strStartsWithAnyInsensitive(string $str, scalar[] $substrings): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string begins with any of $substrings, false otherwise.

- case-insensitive

**Parameters:**
- `string $str <p>The input string.</p>`
- `scalar[] $substrings <p>Substrings to look for.</p>`

**Return:**
- `bool <p>Whether or not $str starts with $substring.</p>`

--------

## strSubstrAfterFirstSeparatorInsensitive(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after the first occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strSubstrAfterLastSeparatorInsensitive(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after the last occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strSubstrBeforeFirstSeparatorInsensitive(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring before the first occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strSubstrBeforeLastSeparatorInsensitive(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring before the last occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strSubstrFirstInsensitive(string $str, string $needle, bool $before_needle, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after (or before via "$before_needle") the first occurrence of the "$needle".

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $needle <p>The string to look for.</p>`
- `bool $before_needle [optional] <p>Default: false</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strSubstrLastInsensitive(string $str, string $needle, bool $before_needle, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after (or before via "$before_needle") the last occurrence of the "$needle".

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $needle <p>The string to look for.</p>`
- `bool $before_needle [optional] <p>Default: false</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## strLastChar(string $str, int $n, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the last $n characters of the string.

**Parameters:**
- `string $str <p>The input string.</p>`
- `int $n <p>Number of characters to retrieve from the end.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## str_limit(string $str, int $length, string $str_add_on, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Limit the number of characters in a string.

**Parameters:**
- `T $str <p>The input string.</p>`
- `int<1, max> $length [optional] <p>Default: 100</p>`
- `string $str_add_on [optional] <p>Default: …</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strLimitAfterWord(string $str, int $length, string $str_add_on, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Limit the number of characters in a string, but also after the next word.

EXAMPLE: <code>UTF8::strLimitAfterWord('fòô bàř fòô', 8, ''); // 'fòô bàř'</code>

**Parameters:**
- `T $str <p>The input string.</p>`
- `int<1, max> $length [optional] <p>Default: 100</p>`
- `string $str_add_on [optional] <p>Default: …</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strLongestCommonPrefix(string $str1, string $str2, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the longest common prefix getSubstringBetween the $str1 and $str2.

**Parameters:**
- `string $str1 <p>The input sting.</p>`
- `string $str2 <p>Second string for comparison.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strLongestCommonSubstring(string $str1, string $str2, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the longest common substring getSubstringBetween the $str1 and $str2.

In the case of ties, it returns that which occurs first.

**Parameters:**
- `string $str1`
- `string $str2 <p>Second string for comparison.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>A string with its $str being the longest common substring.</p>`

--------

## strLongestCommonSuffix(string $str1, string $str2, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the longest common suffix getSubstringBetween the $str1 and $str2.

**Parameters:**
- `string $str1`
- `string $str2 <p>Second string for comparison.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string`

--------

## strMatchesPattern(string $str, string $pattern): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if $str matches the supplied pattern, false otherwise.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $pattern <p>Regex pattern to match against.</p>`

**Return:**
- `bool <p>Whether or not $str matches the pattern.</p>`

--------

## str_obfuscate(string $str, float $percent, string $obfuscateChar, string[] $keepChars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string into a obfuscate string.

EXAMPLE: <code>

UTF8::str_obfuscate('lars@moelleken.org', 0.5, '*', ['@', '.']); // e.g. "l***@m**lleke*.*r*"
</code>

**Parameters:**
- `string $str`
- `float $percent`
- `string $obfuscateChar`
- `string[] $keepChars`

**Return:**
- `string <p>The obfuscate string.</p>`

--------

## strOffsetExists(string $str, int $offset, string $encoding): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns whether or not a character exists at an index. Offsets may be
negative to count from the last character in the string. Implements
part of the ArrayAccess interface.

**Parameters:**
- `string $str <p>The input string.</p>`
- `int $offset <p>The index to check.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `bool <p>Whether or not the index exists.</p>`

--------

## strOffsetGet(string $str, int $index, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the character at the given index. Offsets may be negative to
count from the last character in the string. Implements part of the
ArrayAccess interface, and throws an OutOfBoundsException if the index
does not exist.

**Parameters:**
- `string $str <p>The input string.</p>`
- `int<1, max> $index <p>The <strong>index</strong> from which to retrieve the char.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The character at the specified index.</p>`

--------

## strPad(string $str, int $pad_length, string $pad_string, int|string $pad_type, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Pad a UTF-8 string to a given length with another string.

EXAMPLE: <code>UTF8::strPad('中文空白', 10, '_', STR_PAD_BOTH); // '___中文空白___'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `int $pad_length <p>The length of return string.</p>`
- `string $pad_string [optional] <p>String to use for padding the input string.</p>`
- `int|string $pad_type [optional] <p>
Can be <strong>STR_PAD_RIGHT</strong> (default), [or string "right"]<br>
<strong>STR_PAD_LEFT</strong> [or string "left"] or<br>
<strong>STR_PAD_BOTH</strong> [or string "both"]
</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>Returns the padded string.</p>`

--------

## strPadBoth(string $str, int $length, string $pad_str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string of a given length such that both sides of the
string are padded. Alias for "UTF8::strPad()" with a $pad_type of 'both'.

**Parameters:**
- `string $str`
- `int $length <p>Desired string length after padding.</p>`
- `string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The string with padding applied.</p>`

--------

## strPadLeft(string $str, int $length, string $pad_str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string of a given length such that the beginning of the
string is padded. Alias for "UTF8::strPad()" with a $pad_type of 'left'.

**Parameters:**
- `string $str`
- `int $length <p>Desired string length after padding.</p>`
- `string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The string with left padding.</p>`

--------

## strPadRight(string $str, int $length, string $pad_str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a new string of a given length such that the end of the string
is padded. Alias for "UTF8::strPad()" with a $pad_type of 'right'.

**Parameters:**
- `string $str`
- `int $length <p>Desired string length after padding.</p>`
- `string $pad_str [optional] <p>String used to pad, defaults to space. Default: ' '</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The string with right padding.</p>`

--------

## str_repeat(string $str, int $multiplier): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Repeat a string.

EXAMPLE: <code>UTF8::strRepeat("°~\xf0\x90\x28\xbc", 2); // '°~ð(¼°~ð(¼'</code>

**Parameters:**
- `T $str <p>
The string to be repeated.
</p>`
- `int<1, max> $multiplier <p>
Number of time the input string should be
repeated.
</p>
<p>
multiplier has to be greater than or equal to 0.
If the multiplier is set to 0, the function
will return an empty string.
</p>`

**Return:**
- `string <p>The repeated string.</p>`

--------

## str_replace_beginning(string $str, string $search, string $replacement): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces $search from the beginning of string with $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The string to search for.</p>`
- `string $replacement <p>The replacement.</p>`

**Return:**
- `string <p>A string after the replacements.</p>`

--------

## str_replace_ending(string $str, string $search, string $replacement): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replaces $search from the ending of string with $replacement.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $search <p>The string to search for.</p>`
- `string $replacement <p>The replacement.</p>`

**Return:**
- `string <p>A string after the replacements.</p>`

--------

## str_replace_first(string $search, string $replace, string $subject): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replace the first "$search"-term with the "$replace"-term.

**Parameters:**
- `string $search`
- `string $replace`
- `string $subject`

**Return:**
- `string`

--------

## str_replace_last(string $search, string $replace, string $subject): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replace the last "$search"-term with the "$replace"-term.

**Parameters:**
- `string $search`
- `string $replace`
- `string $subject`

**Return:**
- `string`

--------

## str_shuffle(string $str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Shuffles all the characters in the string.

INFO: uses random algorithm which is weak for cryptography purposes

EXAMPLE: <code>UTF8::str_shuffle('fòô bàř fòô'); // 'àòôřb ffòô '</code>

**Parameters:**
- `T $str <p>The input string</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The shuffled string.</p>`

--------

## str_slice(string $str, int $start, int|null $end, string $encoding): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the substring beginning at $start, and up to, but not including
the index specified by $end. If $end is omitted, the function extracts
the remaining string. If $end is negative, it is computed from the end
of the string.

**Parameters:**
- `string $str`
- `int $start <p>Initial index from which to begin extraction.</p>`
- `int|null $end [optional] <p>Index at which to end extraction. Default: null</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `false|string <p>The extracted substring.</p><p>If <i>str</i> is shorter than <i>start</i>
characters long, <b>FALSE</b> will be returned.`

--------

## str_snakeize(string $str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string to e.g.: "snake_case"

**Parameters:**
- `string $str`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>A string in snake_case.</p>`

--------

## str_sort(string $str, bool $unique, bool $desc): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Sort all characters according to code points.

EXAMPLE: <code>UTF8::str_sort('  -ABC-中文空白-  '); // '    ---ABC中文白空'</code>

**Parameters:**
- `string $str <p>A UTF-8 string.</p>`
- `bool $unique <p>Sort unique. If <strong>true</strong>, repeated characters are ignored.</p>`
- `bool $desc <p>If <strong>true</strong>, will sort characters in reverse code point order.</p>`

**Return:**
- `string <p>A string of sorted characters.</p>`

--------

## strSplit(int|string $str, int $length, bool $clean_utf8, bool $try_to_use_mb_functions): list<string>
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string to an array of unicode characters.

EXAMPLE: <code>UTF8::strSplit('中文空白'); // array('中', '文', '空', '白')</code>

**Parameters:**
- `int|string $str <p>The string or int to split into array.</p>`
- `int<1, max> $length [optional] <p>Max character length of each array
element.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>`
- `bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use
"mb_substr"</p>`

**Return:**
- `list<string> <p>An array containing chunks of chars from the input.</p>`

--------

## strSplitArray(int[]|string[] $input, int $length, bool $clean_utf8, bool $try_to_use_mb_functions): list<list<string>>
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string to an array of Unicode characters.

EXAMPLE: <code>
UTF8::strSplitArray(['中文空白', 'test'], 2); // [['中文', '空白'], ['te', 'st']]
</code>

**Parameters:**
- `int[]|string[] $input <p>The string[] or int[] to split into array.</p>`
- `int<1, max> $length [optional] <p>Max character length of each array
element.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>`
- `bool $try_to_use_mb_functions [optional] <p>Set to false, if you don't want to use
"mb_substr"</p>`

**Return:**
- `list<list<string>> <p>An array containing chunks of the input.</p>`

--------

## strSplit_pattern(string $str, string $pattern, int $limit): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Splits the string with the provided regular expression, returning an
array of strings. An optional integer $limit will truncate the
results.

**Parameters:**
- `string $str`
- `string $pattern <p>The regex with which to split the string.</p>`
- `int $limit [optional] <p>Maximum number of results to return. Default: -1 === no limit</p>`

**Return:**
- `string[] <p>An array of strings.</p>`

--------

## str_starts_with(string $haystack, string $needle): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Check if the string starts with the given substring.

EXAMPLE: <code>
UTF8::str_starts_with('ΚόσμεMiddleEnd', 'Κόσμε'); // true
UTF8::str_starts_with('ΚόσμεMiddleEnd', 'κόσμε'); // false
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `bool`

--------

## str_starts_with_any(string $str, scalar[] $substrings): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns true if the string begins with any of $substrings, false otherwise.

- case-sensitive

**Parameters:**
- `string $str <p>The input string.</p>`
- `scalar[] $substrings <p>Substrings to look for.</p>`

**Return:**
- `bool <p>Whether or not $str starts with $substring.</p>`

--------

## str_substr_after_first_separator(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after the first occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_substr_after_last_separator(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after the last occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_substr_before_first_separator(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring before the first occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_substr_before_last_separator(string $str, string $separator, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring before the last occurrence of a separator.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $separator <p>The string separator.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_substr_first(string $str, string $needle, bool $before_needle, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after (or before via "$before_needle") the first occurrence of the "$needle".

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $needle <p>The string to look for.</p>`
- `bool $before_needle [optional] <p>Default: false</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_substr_last(string $str, string $needle, bool $before_needle, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Gets the substring after (or before via "$before_needle") the last occurrence of the "$needle".

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $needle <p>The string to look for.</p>`
- `bool $before_needle [optional] <p>Default: false</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string`

--------

## str_surround(string $str, string $substring): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Surrounds $str with the given substring.

**Parameters:**
- `T $str`
- `TSub $substring <p>The substring to add to both sides.</p>`

**Return:**
- `string <p>A string with the substring both prepended and appended.</p>`

--------

## str_titleize(string $str, string[]|null $ignore, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length, bool $use_trim_first, string|null $word_define_chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a trimmed string with the first letter of each word capitalized.

Also accepts an array, $ignore, allowing you to list words not to be
capitalized.

**Parameters:**
- `string $str`
- `string[]|null $ignore [optional] <p>An array of words not to capitalize or
null. Default: null</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the
string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az,
el, lt, tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length:
e.g. ẞ -> ß</p>`
- `bool $use_trim_first [optional] <p>true === trim the input string,
first</p>`
- `string|null $word_define_chars [optional] <p>An string of chars that will be used as
whitespace separator === words.</p>`

**Return:**
- `string <p>The titleized string.</p>`

--------

## str_titleize_for_humans(string $str, string[] $ignore, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a trimmed string in proper title case.

Also accepts an array, $ignore, allowing you to list words not to be
capitalized.

Adapted from John Gruber's script.

**Parameters:**
- `string $str`
- `string[] $ignore <p>An array of words not to capitalize.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The titleized string.</p>`

--------

## str_to_binary(string $str): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get a binary representation of a specific string.

EXAPLE: <code>UTF8::str_to_binary('😃'); // '11110000100111111001100010000011'</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `false|string <p>false on error</p>`

--------

## str_to_lines(string $str, bool $remove_empty_values, int|null $remove_short_values): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `string $str`
- `bool $remove_empty_values <p>Remove empty values.</p>`
- `int|null $remove_short_values <p>The min. string length or null to disable</p>`

**Return:**
- `string[]`

--------

## strToWords(string $str, string $char_list, bool $remove_empty_values, int|null $remove_short_values): list<string>
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string into an array of words.

EXAMPLE: <code>UTF8::strToWords('中文空白 oöäü#s', '#') // array('', '中文空白', ' ', 'oöäü#s', '')</code>

**Parameters:**
- `string $str`
- `string $char_list <p>Additional chars for the definition of "words".</p>`
- `bool $remove_empty_values <p>Remove empty values.</p>`
- `int|null $remove_short_values <p>The min. string length or null to disable</p>`

**Return:**
- `list<string>`

--------

## str_truncate(string $str, int $length, string $substring, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Truncates the string to a given length. If $substring is provided, and
truncating occurs, the string is further truncated so that the substring
may be appended without exceeding the desired length.

**Parameters:**
- `string $str`
- `int $length <p>Desired length of the truncated string.</p>`
- `string $substring [optional] <p>The substring to append if it can fit. Default: ''</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`

**Return:**
- `string <p>A string after truncating.</p>`

--------

## str_truncate_safe(string $str, int $length, string $substring, string $encoding, bool $ignore_do_not_split_words_for_one_word): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Truncates the string to a given length, while ensuring that it does not
split words. If $substring is provided, and truncating occurs, the
string is further truncated so that the substring may be appended without
exceeding the desired length.

**Parameters:**
- `string $str`
- `int $length <p>Desired length of the truncated string.</p>`
- `string $substring [optional] <p>The substring to append if it can fit.
Default:
''</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`
- `bool $ignore_do_not_split_words_for_one_word [optional] <p>Default: false</p>`

**Return:**
- `string <p>A string after truncating.</p>`

--------

## str_underscored(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a lowercase and trimmed string separated by underscores.

Underscores are inserted before uppercase characters (with the exception
of the first character of the string), and in place of spaces as well as
dashes.

**Parameters:**
- `string $str`

**Return:**
- `string <p>The underscored string.</p>`

--------

## str_upper_camelize(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns an UpperCamelCase version of the supplied string. It trims
surrounding spaces, capitalizes letters following digits, spaces, dashes
and underscores, and removes spaces, dashes, underscores.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Default: 'UTF-8'</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>A string in UpperCamelCase.</p>`

--------

## str_word_count(string $str, int $format, string $char_list): int|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get the number of words in a specific string.

EXAMPLES: <code>
// format: 0 -> return only word count (int)
//
UTF8::str_word_count('中文空白 öäü abc#c'); // 4
UTF8::str_word_count('中文空白 öäü abc#c', 0, '#'); // 3

// format: 1 -> return words (array)
//
UTF8::str_word_count('中文空白 öäü abc#c', 1); // array('中文空白', 'öäü', 'abc', 'c')
UTF8::str_word_count('中文空白 öäü abc#c', 1, '#'); // array('中文空白', 'öäü', 'abc#c')

// format: 2 -> return words with offset (array)
//
UTF8::str_word_count('中文空白 öäü ab#c', 2); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc', 13 => 'c')
UTF8::str_word_count('中文空白 öäü ab#c', 2, '#'); // array(0 => '中文空白', 5 => 'öäü', 9 => 'abc#c')
</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `0|1|2 $format [optional] <p>
<strong>0</strong> => return a number of words (default)<br>
<strong>1</strong> => return an array of words<br>
<strong>2</strong> => return an array of words with word-offset as key
</p>`
- `string $char_list [optional] <p>Additional chars that contains to words and do not start a new word.</p>`

**Return:**
- `int|string[] <p>The number of words in the string.</p>`

--------

## strcasecmp(string $str1, string $str2, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Case-insensitive string comparison.

INFO: Case-insensitive version of UTF8::strcmp()

EXAMPLE: <code>UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn", "Iñtërnâtiôn\nàlizætiøn"); // 0</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2,<br>
<strong>0</strong> if they are equal`

--------

## strcmp(string $str1, string $str2): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Case-sensitive string comparison.

EXAMPLE: <code>UTF8::strcmp("iñtërnâtiôn\nàlizætiøn", "iñtërnâtiôn\nàlizætiøn"); // 0</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`

**Return:**
- `int <strong>&lt; 0</strong> if str1 is less than str2<br>
<strong>&gt; 0</strong> if str1 is greater than str2<br>
<strong>0</strong> if they are equal`

--------

## strcspn(string $str, string $char_list, int $offset, int|null $length, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find length of initial segment not matching mask.

**Parameters:**
- `string $str`
- `string $char_list`
- `int $offset`
- `int|null $length`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int`

--------

## string(int|int[]|string|string[] $intOrHex): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Create a UTF-8 string from code points.

INFO: opposite to UTF8::codepoints()

EXAMPLE: <code>UTF8::string(array(246, 228, 252)); // 'öäü'</code>

**Parameters:**
- `int[]|numeric-string[]|int|numeric-string $intOrHex <p>Integer or Hexadecimal codepoints.</p>`

**Return:**
- `string <p>A UTF-8 encoded string.</p>`

--------

## hasBom(string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks if string starts with "BOM" (Byte Order Mark Character) character.

EXAMPLE: <code>UTF8::hasBom("\xef\xbb\xbf foobar"); // true</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `bool <p>
<strong>true</strong> if the string has BOM at the start,<br>
<strong>false</strong> otherwise
</p>`

--------

## strip_tags(string $str, string|null $allowable_tags, bool $clean_utf8): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Strip HTML and PHP tags from a string + clean invalid UTF-8.

EXAMPLE: <code>UTF8::strip_tags("<span>κόσμε\xa0\xa1</span>"); // 'κόσμε'</code>

**Parameters:**
- `string $str <p>
The input string.
</p>`
- `string|null $allowable_tags [optional] <p>
You can use the optional second parameter to specify tags which should
not be stripped.
</p>
<p>
HTML comments and PHP tags are also stripped. This is hardcoded and
can not be changed with allowable_tags.
</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `string <p>The stripped string.</p>`

--------

## strip_whitespace(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Strip all whitespace characters. This includes tabs and newline
characters, as well as multibyte whitespace such as the thin space
and ideographic space.

EXAMPLE: <code>UTF8::strip_whitespace('   Ο     συγγραφέας  '); // 'Οσυγγραφέας'</code>

**Parameters:**
- `string $str`

**Return:**
- `string`

--------

## stripos(string $haystack, string $needle, int $offset, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the first occurrence of a substring in a string, case-insensitive.

INFO: use UTF8::stripos_in_byte() for the byte-length

EXAMPLE: <code>UTF8::stripos('aσσb', 'ΣΣ'); // 1</code> (σσ == ΣΣ)

**Parameters:**
- `string $haystack <p>The string from which to get the position of the first occurrence of needle.</p>`
- `string $needle <p>The string to find in haystack.</p>`
- `int $offset [optional] <p>The position in haystack to start searching.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int Return the <strong>(int)</strong> numeric position of the first occurrence of needle in the
haystack string,<br> or <strong>false</strong> if needle is not found`

--------

## stripos_in_byte(string $haystack, string $needle, int $offset): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the first occurrence of a substring in a string, case-insensitive.

**Parameters:**
- `string $haystack <p>
The string being checked.
</p>`
- `string $needle <p>
The position counted from the beginning of haystack.
</p>`
- `int $offset [optional] <p>
The search offset. If it is not specified, 0 is used.
</p>`

**Return:**
- `false|int <p>The numeric position of the first occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>`

--------

## stristr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns all of haystack starting from and including the first occurrence of needle to the end.

EXAMPLE: <code>
$str = 'iñtërnâtiônàlizætiøn';
$search = 'NÂT';

UTF8::stristr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::stristr($str, $search, true)); // 'iñtër'
</code>

**Parameters:**
- `string $haystack <p>The input string. Must be valid UTF-8.</p>`
- `string $needle <p>The string to look for. Must be valid UTF-8.</p>`
- `bool $before_needle [optional] <p>
If <b>TRUE</b>, it returns the part of the
haystack before the first occurrence of the needle (excluding the needle).
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|string <p>A sub-string,<br>or <strong>false</strong> if needle is not found.</p>`

--------

## strlen(string $str, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get the string length, not the byte-length!

INFO: use UTF8::strwidth() for the char-length

EXAMPLE: <code>UTF8::strlen("Iñtërnâtiôn\xE9àlizætiøn")); // 20</code>

**Parameters:**
- `string $str <p>The string being checked for length.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int <p>
The number <strong>(int)</strong> of characters in the string $str having character encoding
$encoding.
(One multi-byte character counted as +1).
<br>
Can return <strong>false</strong>, if e.g. mbstring is not installed and we process invalid
chars.
</p>`

--------

## strlenInByte(string $str): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get string length in byte.

**Parameters:**
- `string $str`

**Return:**
- `int`

--------

## strnatcasecmp(string $str1, string $str2, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Case-insensitive string comparisons using a "natural order" algorithm.

INFO: natural order version of UTF8::strcasecmp()

EXAMPLES: <code>
UTF8::strnatcasecmp('2', '10Hello WORLD 中文空白!'); // -1
UTF8::strcasecmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1

UTF8::strnatcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcasecmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <strong>&lt; 0</strong> if str1 is less than str2<br>
<strong>&gt; 0</strong> if str1 is greater than str2<br>
<strong>0</strong> if they are equal`

--------

## strnatcmp(string $str1, string $str2): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
String comparisons using a "natural order" algorithm

INFO: natural order version of UTF8::strcmp()

EXAMPLES: <code>
UTF8::strnatcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // -1
UTF8::strcmp('2Hello world 中文空白!', '10Hello WORLD 中文空白!'); // 1

UTF8::strnatcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // 1
UTF8::strcmp('10Hello world 中文空白!', '2Hello WORLD 中文空白!'); // -1
</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`

**Return:**
- `int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2;<br>
<strong>0</strong> if they are equal`

--------

## strncasecmp(string $str1, string $str2, int $len, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Case-insensitive string comparison of the first n characters.

EXAMPLE: <code>
UTF8::strcasecmp("iñtërnâtiôn\nàlizætiøn321", "iñtërnâtiôn\nàlizætiøn123", 5); // 0
</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`
- `int $len <p>The length of strings to be used in the comparison.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
<strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
<strong>0</strong> if they are equal`

--------

## strncmp(string $str1, string $str2, int $len, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
String comparison of the first n characters.

EXAMPLE: <code>
UTF8::strncmp("Iñtërnâtiôn\nàlizætiøn321", "Iñtërnâtiôn\nàlizætiøn123", 5); // 0
</code>

**Parameters:**
- `string $str1 <p>The first string.</p>`
- `string $str2 <p>The second string.</p>`
- `int $len <p>Number of characters to use in the comparison.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <strong>&lt; 0</strong> if <i>str1</i> is less than <i>str2</i>;<br>
<strong>&gt; 0</strong> if <i>str1</i> is greater than <i>str2</i>;<br>
<strong>0</strong> if they are equal`

--------

## strpbrk(string $haystack, string $char_list): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Search a string for any of a set of characters.

EXAMPLE: <code>UTF8::strpbrk('-中文空白-', '白'); // '白-'</code>

**Parameters:**
- `string $haystack <p>The string where char_list is looked for.</p>`
- `string $char_list <p>This parameter is case-sensitive.</p>`

**Return:**
- `false|string <p>The string starting from the character found, or false if it is not found.</p>`

--------

## strpos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the first occurrence of a substring in a string.

INFO: use UTF8::strpos_in_byte() for the byte-length

EXAMPLE: <code>UTF8::strpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 8</code>

**Parameters:**
- `string $haystack <p>The string from which to get the position of the first occurrence of needle.</p>`
- `int|string $needle <p>The string to find in haystack.<br>Or a code point as int.</p>`
- `int $offset [optional] <p>The search offset. If it is not specified, 0 is used.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int The <strong>(int)</strong> numeric position of the first occurrence of needle in the haystack
string.<br> If needle is not found it returns false.`

--------

## strpos_in_byte(string $haystack, string $needle, int $offset): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the first occurrence of a substring in a string.

**Parameters:**
- `string $haystack <p>
The string being checked.
</p>`
- `string $needle <p>
The position counted from the beginning of haystack.
</p>`
- `int $offset [optional] <p>
The search offset. If it is not specified, 0 is used.
</p>`

**Return:**
- `false|int <p>The numeric position of the first occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>`

--------

## strrchr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the last occurrence of a character in a string within another.

EXAMPLE: <code>UTF8::strrchr('κόσμεκόσμε-äöü', 'κόσμε'); // 'κόσμε-äöü'</code>

**Parameters:**
- `string $haystack <p>The string from which to get the last occurrence of needle.</p>`
- `string $needle <p>The string to find in haystack</p>`
- `bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the last occurrence of needle.
If set to false, it returns all of haystack
from the last occurrence of needle to the end,
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|string <p>The portion of haystack or false if needle is not found.</p>`

--------

## strrev(string $str, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Reverses characters order in the string.

EXAMPLE: <code>UTF8::strrev('κ-öäü'); // 'üäö-κ'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>The string with characters in the reverse sequence.</p>`

--------

## strrichr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the last occurrence of a character in a string within another, case-insensitive.

EXAMPLE: <code>UTF8::strrichr('Aκόσμεκόσμε-äöü', 'aκόσμε'); // 'Aκόσμεκόσμε-äöü'</code>

**Parameters:**
- `string $haystack <p>The string from which to get the last occurrence of needle.</p>`
- `string $needle <p>The string to find in haystack.</p>`
- `bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the last occurrence of needle.
If set to false, it returns all of haystack
from the last occurrence of needle to the end,
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|string <p>The portion of haystack or<br>false if needle is not found.</p>`

--------

## strripos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the last occurrence of a substring in a string, case-insensitive.

EXAMPLE: <code>UTF8::strripos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13</code>

**Parameters:**
- `string $haystack <p>The string to look in.</p>`
- `int|string $needle <p>The string to look for.</p>`
- `int $offset [optional] <p>Number of characters to ignore in the beginning or end.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
string.<br>If needle is not found, it returns false.</p>`

--------

## strripos_in_byte(string $haystack, string $needle, int $offset): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Finds position of last occurrence of a string within another, case-insensitive.

**Parameters:**
- `string $haystack <p>
The string from which to get the position of the last occurrence
of needle.
</p>`
- `string $needle <p>
The string to find in haystack.
</p>`
- `int $offset [optional] <p>
The position in haystack
to start searching.
</p>`

**Return:**
- `false|int <p>eturn the numeric position of the last occurrence of needle in the
haystack string, or false if needle is not found.</p>`

--------

## strrpos(string $haystack, int|string $needle, int $offset, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the last occurrence of a substring in a string.

EXAMPLE: <code>UTF8::strrpos('ABC-ÖÄÜ-中文空白-中文空白', '中'); // 13</code>

**Parameters:**
- `string $haystack <p>The string being checked, for the last occurrence of needle</p>`
- `int|string $needle <p>The string to find in haystack.<br>Or a code point as int.</p>`
- `int $offset [optional] <p>May be specified to begin searching an arbitrary number of characters
into the string. Negative values will stop searching at an arbitrary point prior to
the end of the string.
</p>`
- `string $encoding [optional] <p>Set the charset.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int <p>The <strong>(int)</strong> numeric position of the last occurrence of needle in the haystack
string.<br>If needle is not found, it returns false.</p>`

--------

## strrpos_in_byte(string $haystack, string $needle, int $offset): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Find the position of the last occurrence of a substring in a string.

**Parameters:**
- `string $haystack <p>
The string being checked, for the last occurrence
of needle.
</p>`
- `string $needle <p>
The string to find in haystack.
</p>`
- `int $offset [optional] <p>May be specified to begin searching an arbitrary number of characters into
the string. Negative values will stop searching at an arbitrary point
prior to the end of the string.
</p>`

**Return:**
- `false|int <p>The numeric position of the last occurrence of needle in the
haystack string. If needle is not found, it returns false.</p>`

--------

## strspn(string $str, string $mask, int $offset, int|null $length, string $encoding): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Finds the length of the initial segment of a string consisting entirely of characters contained within a given
mask.

EXAMPLE: <code>UTF8::strspn('iñtërnâtiônàlizætiøn', 'itñ'); // '3'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $mask <p>The mask of chars</p>`
- `int $offset [optional]`
- `int|null $length [optional]`
- `string $encoding [optional] <p>Set the charset.</p>`

**Return:**
- `false|int`

--------

## strstr(string $haystack, string $needle, bool $before_needle, string $encoding, bool $clean_utf8): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns part of haystack string from the first occurrence of needle to the end of haystack.

EXAMPLE: <code>
$str = 'iñtërnâtiônàlizætiøn';
$search = 'nât';

UTF8::strstr($str, $search)); // 'nâtiônàlizætiøn'
UTF8::strstr($str, $search, true)); // 'iñtër'
</code>

**Parameters:**
- `string $haystack <p>The input string. Must be valid UTF-8.</p>`
- `string $needle <p>The string to look for. Must be valid UTF-8.</p>`
- `bool $before_needle [optional] <p>
If <b>TRUE</b>, strstr() returns the part of the
haystack before the first occurrence of the needle (excluding the needle).
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|string <p>A sub-string,<br>or <strong>false</strong> if needle is not found.</p>`

--------

## strstr_in_byte(string $haystack, string $needle, bool $before_needle): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Finds first occurrence of a string within another.

**Parameters:**
- `string $haystack <p>
The string from which to get the first occurrence
of needle.
</p>`
- `string $needle <p>
The string to find in haystack.
</p>`
- `bool $before_needle [optional] <p>
Determines which portion of haystack
this function returns.
If set to true, it returns all of haystack
from the beginning to the first occurrence of needle.
If set to false, it returns all of haystack
from the first occurrence of needle to the end,
</p>`

**Return:**
- `false|string <p>The portion of haystack,
or false if needle is not found.</p>`

--------

## strtocasefold(string $str, bool $full, bool $clean_utf8, string $encoding, string|null $lang, bool $lower): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Unicode transformation for case-less matching.

EXAMPLE: <code>UTF8::strtocasefold('ǰ◌̱'); // 'ǰ◌̱'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $full [optional] <p>
<b>true</b>, replace full case folding chars (default)<br>
<b>false</b>, use only limited static array [UTF8::$COMMON_CASE_FOLD]
</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string $encoding [optional] <p>Set the charset.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt, tr</p>`
- `bool $lower [optional] <p>Use lowercase string, otherwise use uppercase string. PS: uppercase
is for some languages better ...</p>`

**Return:**
- `string`

--------

## strtolower(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Make a string lowercase.

EXAMPLE: <code>UTF8::strtolower('DÉJÀ Σσς Iıİi'); // 'déjà σσς iıii'</code>

**Parameters:**
- `string $str <p>The string being lowercased.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>String with all alphabetic characters converted to lowercase.</p>`

--------

## strtoupper(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Make a string uppercase.

EXAMPLE: <code>UTF8::strtoupper('Déjà Σσς Iıİi'); // 'DÉJÀ ΣΣΣ IIİI'</code>

**Parameters:**
- `string $str <p>The string being uppercased.</p>`
- `string $encoding [optional] <p>Set the charset.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>String with all alphabetic characters converted to uppercase.</p>`

--------

## strtr(string $str, string|string[] $from, string|string[] $to): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Translate characters or replace sub-strings.

EXAMPLE:
<code>
$array = [
    'Hello'   => '○●◎',
    '中文空白' => 'earth',
];
UTF8::strtr('Hello 中文空白', $array); // '○●◎ earth'
</code>

**Parameters:**
- `string $str <p>The string being translated.</p>`
- `string|string[] $from <p>The string replacing from.</p>`
- `string|string[] $to [optional] <p>The string being translated to to.</p>`

**Return:**
- `string <p>This function returns a copy of str, translating all occurrences of each character in "from"
to the corresponding character in "to".</p>`

--------

## strwidth(string $str, string $encoding, bool $clean_utf8): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Return the width of a string.

INFO: use UTF8::strlen() for the byte-length

EXAMPLE: <code>UTF8::strwidth("Iñtërnâtiôn\xE9àlizætiøn")); // 21</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `int`

--------

## substr(string $str, int $offset, int|null $length, string $encoding, bool $clean_utf8): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get part of a string.

EXAMPLE: <code>UTF8::substr('中文空白', 1, 2); // '文空'</code>

**Parameters:**
- `string $str <p>The string being checked.</p>`
- `int $offset <p>The first position used in str.</p>`
- `int|null $length [optional] <p>The maximum length of the returned string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|string The portion of <i>str</i> specified by the <i>offset</i> and
<i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
characters long, <b>FALSE</b> will be returned.`

--------

## substr_compare(string $str1, string $str2, int $offset, int|null $length, bool $case_insensitivity, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Binary-safe comparison of two strings from an offset, up to a length of characters.

EXAMPLE: <code>
UTF8::substr_compare("○●◎\r", '●◎', 0, 2); // -1
UTF8::substr_compare("○●◎\r", '◎●', 1, 2); // 1
UTF8::substr_compare("○●◎\r", '●◎', 1, 2); // 0
</code>

**Parameters:**
- `string $str1 <p>The main string being compared.</p>`
- `string $str2 <p>The secondary string being compared.</p>`
- `int $offset [optional] <p>The start position for the comparison. If negative, it starts
counting from the end of the string.</p>`
- `int|null $length [optional] <p>The length of the comparison. The default value is the largest
of the length of the str compared to the length of main_str less the
offset.</p>`
- `bool $case_insensitivity [optional] <p>If case_insensitivity is TRUE, comparison is case
insensitive.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int <strong>&lt; 0</strong> if str1 is less than str2;<br>
<strong>&gt; 0</strong> if str1 is greater than str2,<br>
<strong>0</strong> if they are equal`

--------

## substr_count(string $haystack, string $needle, int $offset, int|null $length, string $encoding, bool $clean_utf8): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Count the number of substring occurrences.

EXAMPLE: <code>UTF8::substr_count('中文空白', '文空', 1, 2); // 1</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`
- `int $offset [optional] <p>The offset where to start counting.</p>`
- `int|null $length [optional] <p>
The maximum length after the specified offset to search for the
substring. It outputs a warning if the offset plus the length is
greater than the haystack length.
</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `false|int <p>This functions returns an integer or false if there isn't a string.</p>`

--------

## substr_count_in_byte(string $haystack, string $needle, int $offset, int|null $length): false|int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Count the number of substring occurrences.

**Parameters:**
- `string $haystack <p>
The string being checked.
</p>`
- `string $needle <p>
The string being found.
</p>`
- `int $offset [optional] <p>
The offset where to start counting
</p>`
- `int|null $length [optional] <p>
The maximum length after the specified offset to search for the
substring. It outputs a warning if the offset plus the length is
greater than the haystack length.
</p>`

**Return:**
- `false|int <p>The number of times the
needle substring occurs in the
haystack string.</p>`

--------

## countSubstring(string $str, string $substring, bool $case_sensitive, string $encoding): int
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the number of occurrences of $substring in the given string.

By default, the comparison is case-sensitive, but can be made insensitive
by setting $case_sensitive to false.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $substring <p>The substring to search for.</p>`
- `bool $case_sensitive [optional] <p>Whether or not to enforce case-sensitivity. Default: true</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `int`

--------

## substr_ileft(string $haystack, string $needle): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Removes a prefix ($needle) from the beginning of the string ($haystack), case-insensitive.

EXMAPLE: <code>
UTF8::substr_ileft('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
UTF8::substr_ileft('ΚόσμεMiddleEnd', 'κόσμε'); // 'MiddleEnd'
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `string <p>Return the sub-string.</p>`

--------

## strlenInByte(string $str, int $offset, int|null $length): false|string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Get part of a string process in bytes.

**Parameters:**
- `string $str <p>The string being checked.</p>`
- `int $offset <p>The first position used in str.</p>`
- `int|null $length [optional] <p>The maximum length of the returned string.</p>`

**Return:**
- `false|string <p>The portion of <i>str</i> specified by the <i>offset</i> and
<i>length</i> parameters.</p><p>If <i>str</i> is shorter than <i>offset</i>
characters long, <b>FALSE</b> will be returned.</p>`

--------

## substr_iright(string $haystack, string $needle): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Removes a suffix ($needle) from the end of the string ($haystack), case-insensitive.

EXAMPLE: <code>
UTF8::substr_iright('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
UTF8::substr_iright('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddle'
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `string <p>Return the sub-string.<p>`

--------

## substr_left(string $haystack, string $needle): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Removes a prefix ($needle) from the beginning of the string ($haystack).

EXAMPLE: <code>
UTF8::substr_left('ΚόσμεMiddleEnd', 'Κόσμε'); // 'MiddleEnd'
UTF8::substr_left('ΚόσμεMiddleEnd', 'κόσμε'); // 'ΚόσμεMiddleEnd'
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`

**Return:**
- `string <p>Return the sub-string.</p>`

--------

## substr_replace(string|string[] $str, string|string[] $replacement, int|int[] $offset, int|int[]|null $length, string $encoding): string|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Replace text within a portion of a string.

EXAMPLE: <code>UTF8::substr_replace(array('Iñtërnâtiônàlizætiøn', 'foo'), 'æ', 1); // array('Iæñtërnâtiônàlizætiøn', 'fæoo')</code>

source: https://gist.github.com/stemar/8287074

**Parameters:**
- `TSubReplace $str <p>The input string or an array of stings.</p>`
- `string|string[] $replacement <p>The replacement string or an array of stings.</p>`
- `int|int[] $offset <p>
If start is positive, the replacing will begin at the start'th offset
into string.
<br><br>
If start is negative, the replacing will begin at the start'th character
from the end of string.
</p>`
- `int|int[]|null $length [optional] <p>If given and is positive, it represents the length of the
portion of string which is to be replaced. If it is negative, it
represents the number of characters from the end of string at which to
stop replacing. If it is not given, then it will default to strlen(
string ); i.e. end the replacing at the end of string. Of course, if
length is zero then this function will have the effect of inserting
replacement into string at the given start offset.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string|string[] <p>The result string is returned. If string is an array then array is returned.</p>`

--------

## substr_right(string $haystack, string $needle, string $encoding): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Removes a suffix ($needle) from the end of the string ($haystack).

EXAMPLE: <code>
UTF8::substr_right('BeginMiddleΚόσμε', 'Κόσμε'); // 'BeginMiddle'
UTF8::substr_right('BeginMiddleΚόσμε', 'κόσμε'); // 'BeginMiddleΚόσμε'
</code>

**Parameters:**
- `string $haystack <p>The string to search in.</p>`
- `string $needle <p>The substring to search for.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`

**Return:**
- `string <p>Return the sub-string.</p>`

--------

## swapCase(string $str, string $encoding, bool $clean_utf8): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns a case swapped version of the string.

EXAMPLE: <code>UTF8::swapCase('déJÀ σσς iıII'); // 'DÉjà ΣΣΣ IIii'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `string <p>Each character's case swapped.</p>`

--------

## symfony_polyfill_used(): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Checks whether symfony-polyfills are used.

**Parameters:**
__nothing__

**Return:**
- `bool <p><strong>true</strong> if in use, <strong>false</strong> otherwise</p>`

--------

## tabs_to_spaces(string $str, int $tab_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `string $str`
- `int $tab_length`

**Return:**
- `string`

--------

## titlecase(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Converts the first character of each word in the string to uppercase
and all other chars to lowercase.

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>A string with all characters of $str being title-cased.</p>`

--------

## toAscii(string $str, string $unknown, bool $strict): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string into ASCII.

EXAMPLE: <code>UTF8::toAscii('déjà σσς iıii'); // 'deja sss iiii'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $unknown [optional] <p>Character use if character unknown. (default is ?)</p>`
- `bool $strict [optional] <p>Use "transliterator_transliterate()" from PHP-Intl | WARNING: bad
performance</p>`

**Return:**
- `string`

--------

## to_boolean(bool|float|int|string $str): bool
<a href="#jessegreathouse-php-readme-class-methods">↑</a>


**Parameters:**
- `bool|float|int|string $str`

**Return:**
- `bool`

--------

## to_filename(string $str, bool $use_transliterate, string $fallback_char): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert given string to safe filename (and keep string case).

**Parameters:**
- `string $str`
- `bool $use_transliterate No transliteration, conversion etc. is done by default - unsafe characters are
simply replaced with hyphen.`
- `string $fallback_char`

**Return:**
- `string`

--------

## to_int(string $str): int|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the given string as an integer, or null if the string isn't numeric.

**Parameters:**
- `string $str`

**Return:**
- `int|null <p>null if the string isn't numeric</p>`

--------

## toIso8859(string|string[] $str): string|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Convert a string into "ISO-8859"-encoding (Latin-1).

EXAMPLE: <code>UTF8::toUtf8(UTF8::toIso8859('  -ABC-中文空白-  ')); // '  -ABC-????-  '</code>

**Parameters:**
- `TToIso8859 $str`

**Return:**
- `string|string[]`

--------

## to_string(float|int|object|string|null $input): string|null
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns the given input as string, or null if the input isn't int|float|string
and do not implement the "__toString()" method.

**Parameters:**
- `float|int|object|string|null $input`

**Return:**
- `string|null <p>null if the input isn't int|float|string and has no "__toString()" method</p>`

--------

## toUtf8(string|string[] $str, bool $decode_html_entity_toUtf8): string|string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.

<ul>
<li>It decode UTF-8 codepoints and Unicode escape sequences.</li>
<li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
<li>WARNING: It does not remove invalid UTF-8 characters, so you maybe need to use "UTF8::clean()" for this
case.</li>
</ul>

EXAMPLE: <code>UTF8::toUtf8(["\u0063\u0061\u0074"]); // array('cat')</code>

**Parameters:**
- `TToUtf8 $str <p>Any string or array of strings.</p>`
- `bool $decode_html_entity_toUtf8 <p>Set to true, if you need to decode html-entities.</p>`

**Return:**
- `string|string[] <p>The UTF-8 encoded string</p>`

--------

## toUtf8String(string $str, bool $decode_html_entity_toUtf8): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
This function leaves UTF-8 characters alone, while converting almost all non-UTF8 to UTF8.

<ul>
<li>It decode UTF-8 codepoints and Unicode escape sequences.</li>
<li>It assumes that the encoding of the original string is either WINDOWS-1252 or ISO-8859.</li>
<li>WARNING: It does not remove invalid UTF-8 characters, so you maybe need to use "UTF8::clean()" for this
case.</li>
</ul>

EXAMPLE: <code>UTF8::toUtf8String("\u0063\u0061\u0074"); // 'cat'</code>

**Parameters:**
- `T $str <p>Any string.</p>`
- `bool $decode_html_entity_toUtf8 <p>Set to true, if you need to decode html-entities.</p>`

**Return:**
- `string <p>The UTF-8 encoded string</p>`

--------

## trim(string $str, string|null $chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Strip whitespace or other characters from the beginning and end of a UTF-8 string.

INFO: This is slower then "trim()"

We can only use the original-function, if we use <= 7-Bit in the string / chars
but the check for ASCII (7-Bit) cost more time, then we can safe here.

EXAMPLE: <code>UTF8::trim('   -ABC-中文空白-  '); // '-ABC-中文空白-'</code>

**Parameters:**
- `string $str <p>The string to be trimmed</p>`
- `string|null $chars [optional] <p>Optional characters to be stripped</p>`

**Return:**
- `string <p>The trimmed string.</p>`

--------

## ucfirst(string $str, string $encoding, bool $clean_utf8, string|null $lang, bool $try_to_keep_the_string_length): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Makes string's first char uppercase.

EXAMPLE: <code>UTF8::ucfirst('ñtërnâtiônàlizætiøn foo'); // 'Ñtërnâtiônàlizætiøn foo'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string $encoding [optional] <p>Set the charset for e.g. "mb_" function</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`
- `string|null $lang [optional] <p>Set the language for special cases: az, el, lt,
tr</p>`
- `bool $try_to_keep_the_string_length [optional] <p>true === try to keep the string length: e.g. ẞ
-> ß</p>`

**Return:**
- `string <p>The resulting string with with char uppercase.</p>`

--------

## ucwords(string $str, string[] $exceptions, string $char_list, string $encoding, bool $clean_utf8): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Uppercase for all words in the string.

EXAMPLE: <code>UTF8::ucwords('iñt ërn âTi ônà liz æti øn'); // 'Iñt Ërn ÂTi Ônà Liz Æti Øn'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `string[] $exceptions [optional] <p>Exclusion for some words.</p>`
- `string $char_list [optional] <p>Additional chars that contains to words and do not start a new
word.</p>`
- `string $encoding [optional] <p>Set the charset.</p>`
- `bool $clean_utf8 [optional] <p>Remove non UTF-8 chars from the string.</p>`

**Return:**
- `string`

--------

## urldecode(string $str, bool $multi_decode): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Multi decode HTML entity + fix urlencoded-win1252-chars.

EXAMPLE: <code>UTF8::urldecode('tes%20öäü%20\u00edtest+test'); // 'tes öäü ítest test'</code>

e.g:
'test+test'                     => 'test test'
'D&#252;sseldorf'               => 'Düsseldorf'
'D%FCsseldorf'                  => 'Düsseldorf'
'D&#xFC;sseldorf'               => 'Düsseldorf'
'D%26%23xFC%3Bsseldorf'         => 'Düsseldorf'
'DÃ¼sseldorf'                   => 'Düsseldorf'
'D%C3%BCsseldorf'               => 'Düsseldorf'
'D%C3%83%C2%BCsseldorf'         => 'Düsseldorf'
'D%25C3%2583%25C2%25BCsseldorf' => 'Düsseldorf'

**Parameters:**
- `T $str <p>The input string.</p>`
- `bool $multi_decode <p>Decode as often as possible.</p>`

**Return:**
- `string`

--------

## utf8Decode(string $str, bool $keep_utf8_chars): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Decodes a UTF-8 string to ISO-8859-1.

EXAMPLE: <code>UTF8::encode('UTF-8', UTF8::utf8Decode('-ABC-中文空白-')); // '-ABC-????-'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `bool $keep_utf8_chars`

**Return:**
- `string`

--------

## utf8_encode(string $str): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Encodes an ISO-8859-1 string to UTF-8.

EXAMPLE: <code>UTF8::utf8Decode(UTF8::utf8Encode('-ABC-中文空白-')); // '-ABC-中文空白-'</code>

**Parameters:**
- `string $str <p>The input string.</p>`

**Return:**
- `string`

--------

## whitespace_table(): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns an array with all utf8 whitespace characters.

**Parameters:**
__nothing__

**Return:**
- `string[] An array with all known whitespace characters as values and the type of whitespace as keys
as defined in above URL`

--------

## words_limit(string $str, int $limit, string $str_add_on): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Limit the number of words in a string.

EXAMPLE: <code>UTF8::words_limit('fòô bàř fòô', 2, ''); // 'fòô bàř'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `int<1, max> $limit <p>The limit of words as integer.</p>`
- `string $str_add_on <p>Replacement for the striped string.</p>`

**Return:**
- `string`

--------

## wordwrap(string $str, int $width, string $break, bool $cut): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Wraps a string to a given number of characters

EXAMPLE: <code>UTF8::wordwrap('Iñtërnâtiônàlizætiøn', 2, '<br>', true)); // 'Iñ<br>të<br>rn<br>ât<br>iô<br>nà<br>li<br>zæ<br>ti<br>øn'</code>

**Parameters:**
- `string $str <p>The input string.</p>`
- `int<1, max> $width [optional] <p>The column width.</p>`
- `string $break [optional] <p>The line is broken using the optional break parameter.</p>`
- `bool $cut [optional] <p>
If the cut is set to true, the string is
always wrapped at or before the specified width. So if you have
a word that is larger than the given width, it is broken apart.
</p>`

**Return:**
- `string <p>The given string wrapped at the specified column.</p>`

--------

## wordwrap_per_line(string $str, int $width, string $break, bool $cut, bool $add_final_break, string|null $delimiter): string
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Line-Wrap the string after $limit, but split the string by "$delimiter" before ...
   ... so that we wrap the per line.

**Parameters:**
- `string $str <p>The input string.</p>`
- `int<1, max> $width [optional] <p>The column width.</p>`
- `string $break [optional] <p>The line is broken using the optional break parameter.</p>`
- `bool $cut [optional] <p>
If the cut is set to true, the string is
always wrapped at or before the specified width. So if you have
a word that is larger than the given width, it is broken apart.
</p>`
- `bool $add_final_break [optional] <p>
If this flag is true, then the method will add a $break at the end
of the result string.
</p>`
- `non-empty-string|null $delimiter [optional] <p>
You can change the default behavior, where we split the string by newline.
</p>`

**Return:**
- `string`

--------

## ws(): string[]
<a href="#jessegreathouse-php-readme-class-methods">↑</a>
Returns an array of Unicode White Space characters.

**Parameters:**
__nothing__

**Return:**
- `string[] <p>An array with numeric code point as key and White Space Character as value.</p>`

--------



## Unit Test

1) [Composer](https://getcomposer.org) is a prerequisite for running the tests.

```
composer install
```

2) The tests can be executed by running this command from the root directory:

```bash
./vendor/bin/phpunit
```

### Support

For support and donations please visit [GitHub](https://github.com/jessegreathouse/portable-utf8/) | [Issues](https://github.com/jessegreathouse/portable-utf8/issues) | [PayPal](https://paypal.me/moelleken) | [Patreon](https://www.patreon.com/jessegreathouse).

For status updates and release announcements please visit [Releases](https://github.com/jessegreathouse/portable-utf8/releases) | [Twitter](https://twitter.com/suckup_de) | [Patreon](https://www.patreon.com/jessegreathouse/posts).

For professional support please contact [me](https://about.me/jessegreathouse).

### Thanks

- Thanks to [GitHub](https://github.com) (Microsoft) for hosting the code and a good infrastructure including Issues-Management, etc.
- Thanks to [IntelliJ](https://www.jetbrains.com) as they make the best IDEs for PHP and they gave me an open source license for PhpStorm!
- Thanks to [Travis CI](https://travis-ci.com/) for being the most awesome, easiest continuous integration tool out there!
- Thanks to [StyleCI](https://styleci.io/) for the simple but powerful code style check.
- Thanks to [PHPStan](https://github.com/phpstan/phpstan) && [Psalm](https://github.com/vimeo/psalm) for really great Static analysis tools and for discovering bugs in the code!

### License and Copyright

"Portable UTF8" is free software; you can redistribute it and/or modify it under
the terms of the (at your option):
- [Apache License v2.0](http://apache.org/licenses/LICENSE-2.0.txt), or
- [GNU General Public License v2.0](http://gnu.org/licenses/gpl-2.0.txt).

Unicode handling requires tedious work to be implemented and maintained on the
long run. As such, contributions such as unit tests, bug reports, comments or
patches licensed under both licenses are really welcomed.


[![FOSSA Status](https://app.fossa.io/api/projects/git%2Bgithub.com%2Fjessegreathouse%2Fportable-utf8.svg?type=large)](https://app.fossa.io/projects/git%2Bgithub.com%2Fjessegreathouse%2Fportable-utf8?ref=badge_large)
