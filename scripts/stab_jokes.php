<?php

require __DIR__ . "/../lib/QuoteServer.php";

echo (new QuoteServer())->
    loadFromFile(__DIR__ . '/../quote_lists/stabb.txt')->
    pickRandomQuote() . "\n";