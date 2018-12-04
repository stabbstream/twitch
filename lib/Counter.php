<?php

class Counter
{
    const DATA_COUNT = 'count';

    private $storeDirectory = null;
    private $defaultCounter = null;
    private $currentCounter = null;
    private $currentCount = null;

    /**
     * Counter constructor.
     * @param string $storeDirectory Path to directory to store data in
     */
    public function __construct($storeDirectory)
    {
        $this->storeDirectory = $storeDirectory;
        $this->readAppData();
    }

    /**
     * Reads in the application data
     */
    private function readAppData()
    {
        $fileName = "{$this->storeDirectory}/appData.json";
        if (file_exists($fileName)) {
            $data = json_decode(file_get_contents($fileName), true);
            if (isset($data['default-counter'])) {
                $this->defaultCounter = $data['default-counter'];
            }
        }
    }

    /**
     * Writes out the application data
     */
    private function writeAppData()
    {
        $fileName = "{$this->storeDirectory}/appData.json";
        $data = json_encode([
            'default-counter' => $this->defaultCounter,
        ]);
        file_put_contents($fileName, $data, JSON_PRETTY_PRINT);
    }
 
    /**
     * Validate the data from a counter file
     *
     * @param array $counterData data from counter files
     * @return bool
     */
    private function validateCounterData($counterData)
    {
        if (array_key_exists(self::DATA_COUNT, $counterData) && preg_match('/^\d+$/', $counterData[self::DATA_COUNT])) {
            return true;
        }
        return false;
    }

    /**
     * Creates the file path for a counter file
     *
     * @param string $counterName Name of the counter
     * @return string Path to counter file
     */
    private function getCounterFilePath($counterName)
    {
        return "{$this->storeDirectory}/{$counterName}.json";
    }

    /**
     * Read data from a counter file, returns an empty dataset if no file found
     * @param string $counterName The name of the counter to get
     * @return array The counter data
     * @throws RuntimeException if data is invalid
     */
    private function readCounterData($counterName)
    {
        $fileName = $this->getCounterFilePath($counterName);
        if (file_exists($fileName)) {
            $data = json_decode(file_get_contents($fileName), true);
            if ($this->validateCounterData($data)) {
                return $data;
            } else {
                throw new RuntimeException('Invalid data found for counter: ' . $counterName);
            }
        }
        return [
            self::DATA_COUNT => 0
        ];
    }

    /**
     * Write data to a counter file
     * @param $counterName
     * @param $data
     */
    private function writeCounterData($counterName, $data)
    {
        $fileName = $this->getCounterFilePath($counterName);
        file_put_contents($fileName, json_encode($data, JSON_PRETTY_PRINT));
    }

    /**
     * Performs a manipulation operation to a counter generically
     * @param null|callable $callback
     * @return array The counter data after the operation has been performed
     */
    private function performOperation($callback = null)
    {
        $name = $this->getCurrentCounterName();
        $data = $this->readCounterData($name);
        if ($callback !== null) {
            $callback($data);
        }
        $this->writeCounterData($name, $data);
        $this->currentCount = $data[self::DATA_COUNT];
        return [
            'name' => $name,
            'count' => $this->currentCount,
        ];
    }

    /**
     * Get's the current counter name
     * @return string The counter name
     */
    public function getCurrentCounterName()
    {
        if ($this->currentCounter === null) {
            $this->currentCounter = $this->defaultCounter;
        }
        if ($this->currentCounter === null) {
            throw new RuntimeException("No counter was set");
        }
        return $this->currentCounter;
    }

    /**
     * Increments the counter
     * @return array The counter data after the operation has been performed
     */
    public function increment()
    {
        return $this->performOperation(
            function (&$data) {
                $data[self::DATA_COUNT]++;
            }
        );
    }

    /**
     * Decrements the counter
     * @return array The counter data after the operation has been performed
     */
    public function decrement()
    {
        return $this->performOperation(
            function (&$data) {
                $data[self::DATA_COUNT]--;
                if ($data[self::DATA_COUNT] < 0) {
                    $data[self::DATA_COUNT] = 0;
                }
            }
        );
    }

    /**
     * Resets the counter back to 0
     * @return array The counter data after the operation has been performed
     */
    public function clear()
    {
        return $this->performOperation(
            function (&$data) {
                $data[self::DATA_COUNT] = 0;
            }
        );
    }

    /**
     * Get's the current counter data
     * @return array The counter data after the operation has been performed
     */
    public function getCount()
    {
        if ($this->currentCount) {
            return $this->currentCount;
        }
        return $this->performOperation(null);
    }

    /**
     * Set's the default counter to use
     * @param string $counterName The name of the counter to set
     * @return $this
     */
    public function setDefaultCounterName($counterName)
    {
        $this->defaultCounter = $counterName;
        $this->writeAppData();
        return $this;
    }

    /**
     * Set's the current counter to use
     * @param string $counterName The name of the counter to set
     * @return $this
     */
    public function setCurrentCounterName($counterName)
    {
        $this->currentCounter = $counterName;
        return $this;
    }
}
