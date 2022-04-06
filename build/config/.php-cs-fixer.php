<?php

$finder = PhpCsFixer\Finder::create()
    ->exclude('vendor')
    ->exclude('var')
    ->exclude('generated')
    ->exclude('tests/testdata')
    ->in(__DIR__ . '/../../');

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        '@PSR12' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])->setFinder($finder)
    ->setCacheFile(__DIR__ . '/../../build/cache/.php-cs-fixer.cache');