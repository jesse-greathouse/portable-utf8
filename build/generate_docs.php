<?php

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/vendor/autoload.php';

$readmeText = (new \jessegreathouse\PhpReadmeHelper\GenerateApi())->generate(
    __DIR__ . '/../src/jessegreathouse/helper/UTF8.php',
    __DIR__ . '/docs/base.md'
);

file_put_contents(__DIR__ . '/../README.md', $readmeText);
