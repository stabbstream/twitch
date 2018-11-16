<?php

require __DIR__ . "/../lib/QuoteServer.php";

$list = file(__DIR__ . '/../quote_lists/stabb.txt',  FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$list = array_filter($list);

echo (new QuoteServer())->setQuotes($list)->pickRandomQuote() . "\n";
