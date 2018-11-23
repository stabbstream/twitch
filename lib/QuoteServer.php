<?php

class QuoteServer
{

    /** @var array[string] an array of quotes to serve */
    private $quotes = null;

    /** @var string path to temporary directory */
    private $tmpDir;

    public function __construct()
    {
        $this->tmpDir = realpath(__DIR__ . '/../.tmp');
    }

    /**
     * Get's a file from a file or web path
     * @param string $path
     * @return array
     */
    private function getFile($path) {
        $fileContents = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($fileContents === false) {
            throw new RuntimeException("Failed to get file at: $path");
        }
        return array_filter($fileContents, function($v, $k) {
            return trim($v) !== '';
        }, ARRAY_FILTER_USE_BOTH);
    }

    /**
     * Sets the quotes from a file retrieved from a url, where each line is a quote
     * @param string $url the web path to the file
     * @return self $this
     */
    public function loadFromUrl($url)
    {
        $hash = sha1($url);
        $hashPath = $this->tmpDir . "/$hash";

        if (file_exists($hashPath) && time() - filemtime($hashPath) < 86400) { //One day
            $this->quotes = unserialize(
                file_get_contents($hashPath)
            );
        } else {
            $fileContents = $this->getFile($url);
            file_put_contents($hashPath, serialize($fileContents));
            $this->quotes = $fileContents;
        }
        return $this;
    }

    /**
     * Sets the quotes from a text file, where each line is a quote
     * @param string $filePath the path to the text file
     * @return self $this
     */
    public function loadFromFile($filePath)
    {
        $this->quotes = $this->getFile($filePath);
        return $this;
    }

    /**
     * Sets the quotes from an array, where each index is a quote
     * @param array $quotes
     * @return self $this
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
     * Validates the quote list exists and is valid
     */
    private function validateQuoteList()
    {
        if ($this->quotes === null) {
            throw new RuntimeException("No quote list. Use a load method to initialize quote list");
        }
    }

    /**
     * Returns a random quote from the quote list
     * @return string
     */
    public function pickRandomQuote()
    {
        $this->validateQuoteList();
        return $this->quotes[rand(0, count($this->quotes) - 1)];
    }

    /**
     * Returns the selected quote from the quote list
     * @param $index int The index from the quoteList to return
     * @return string
     */
    public function getQuote($index)
    {
        $this->validateQuoteList();
        if ($index < 0) {
            throw new RuntimeException("The getQuote method requires and index >= 0");
        }
        if ($index >= count($this->quotes)) {
            throw new RuntimeException("The requested quote is greater then the list length (". count($this->quotes) . ")");
        }
        return $this->quotes[$index];
    }
}
