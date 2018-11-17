<?php

require __DIR__ . "/../lib/QuoteServer.php";

echo (new QuoteServer())->
    loadFromUrl('https://raw.githubusercontent.com/stabbstream/twitch/master/quote_lists/stabb.txt')->
    pickRandomQuote() . "\n";