<?php

class QuoteServer
{

    /** @var array[string] an array of quotes to serve */
    private $quotes = null;

    /**
     * Sets the quotes from a text file, where each line is a quote
     * @param $filePath
     * @return $this
     */
    public function loadFromFile($filePath)
    {
        $fileContents = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($fileContents !== false) {
            $this->quotes = array_filter($fileContents);
        } else {
            throw new RuntimeException("Failed to load file: $filePath");
        }
        return $this;
    }

    /**
     * Sets the quotes from an array, where each index is a quote
     * @param array $quotes
     * @return self
     */
    public function loadFromArray($quotes)
    {
        if (is_array($quotes)) {
            $this->quotes = $quotes;
        } else {
            throw new RuntimeException("Item passed into loadFromArray is not an array");
        }
        return $this;
    }

    /**
     * Returns a random quote from the quote list
     * @return string
     */
    public function pickRandomQuote()
    {
        if ($this->quotes === null) {
            throw new RuntimeException("No quote list. Use a load method to initialize quote list");
        }
        return $this->quotes[rand(0, count($this->quotes) - 1)];
    }
}
