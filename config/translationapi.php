<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Sentence separator
    |--------------------------------------------------------------------------
    |
    | This sentence separator is used to determine if a group of words belongs to the same sentence.
    | A sentence has an own price and is billed as a translation item.
    |
    */
    'sentence_separator'    => env('SENTENCE_SEPARATOR', '#LW-Test#'),

    /*
    |--------------------------------------------------------------------------
    | Enable custom observers in the project
    |--------------------------------------------------------------------------
    |
    | This sentence separator is used to determine if a group of words belongs to the same sentence.
    | A sentence has an own price and is billed as a translation item.
    |
    */
    'enable_observers'      => env('ENABLE_OBSERVERS', false),
];
