<?php

class QuoteServer {
    /** @var array[string] an array of quotes to serve */
    private $quotes = null;

    /**
     * Sets the quotes
     * @param array $quotes
     * @return self
     */
    public function setQuotes($quotes) {
        if (is_array($quotes)) {
            $this->quotes = $quotes;
        }
        return $this;
    }

    /**
     * Returns a random quote from the quote list
     * @return string
     */
    public function pickRandomQuote() {
        if ($this->quotes === null) {
            throw new RuntimeException("No quote list. Use setQuotes() method to initialize quote list");
        }
        return $this->quotes[ rand(0, count($this->quotes)) ];
    }
}
