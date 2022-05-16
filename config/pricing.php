<?php


return [
    /*
    |--------------------------------------------------------------------------
    | List of prices per translation item. (All units are in EURO)
    |--------------------------------------------------------------------------
    |
    | Prices for items in the same file
    |
    */
    'word_price'                => env('WORD_PRICE', 0.07),
    'word_repeated_price'       => env('WORD_REPEATED_PRICE', 0.02),
    'sentence_price'            => env('SENTENCE_PRICE', 0),                //Because the sentence is the sum of the words
    'sentence_repeated_price'   => env('SENTENCE_REPEATED_PRICE', 0),       //Because the sentence was already translated before (all words)

    /*
    | Prices for items in other files of the same basket
    */
    'word_file_repeated_price'      => env('WORD_FILE_REPEATED_PRICE', 0.05),
    'sentence_file_repeated_price'  => env('SENTENCE_FILE_REPEATED_PRICE', 0.01),

    /*
    | File formats percent increasing
    */
    'file_format_percent'   => [
        'pdf'   => env('PDF_FILE_FORMAT_PERCENT', 20),
        'psd'   => env('PSD_FILE_FORMAT_PERCENT', 35),
    ],

    /*
    | Target language percent discounts
    */
    'language_percent'  => [
        'es_ES'     => env('ES_ES_LANGUAGE_PERCENT', -20),
    ]
];
