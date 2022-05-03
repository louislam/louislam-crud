<?php

use GD75\DoubleQuoteFixer\DoubleQuoteFixer;

$finder = PhpCsFixer\Finder::create()
    ->notPath("js")
    ->notPath("css")
    ->in(__DIR__);

$config = new PhpCsFixer\Config();

return $config->registerCustomFixers([
    new DoubleQuoteFixer()
])->setRules([
    "@PSR12" => true,
    "no_mixed_echo_print" => true,
    "array_syntax" => [
        "syntax" => "short"
    ],
    "encoding" => true,
    "single_quote" => false,
    "GD75/double_quote_fixer" => true,
    "no_trailing_comma_in_singleline_array" => true,
    "trim_array_spaces" => true,
    "braces" => [
        "position_after_functions_and_oop_constructs" => "same"
    ],
    "multiline_whitespace_before_semicolons" => [
        "strategy" => "no_multi_line"
    ],
    "no_empty_statement" => true,
    "no_singleline_whitespace_before_semicolons" => true,
    "semicolon_after_instruction" => true,
    "no_extra_blank_lines" => [
        "default"
    ]
])->setFinder($finder);

