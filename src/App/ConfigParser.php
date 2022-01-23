<?php

namespace App;

use Exception;

class ConfigParser
{
    /**
     * @var array $config
     */
    public $config = [];

    /**
     * @var ?mixed $result
     */
    private $result = null;

    /**
     * Load files and merge into config
     */
    public function load(string ...$filenames)
    {
        foreach ($filenames as $filename) {
            try {
                if (file_exists($filename)) {
                    $content = file_get_contents($filename);

                    if ($content === false) {
                        throw new Exception('Unable to read file');
                    }

                    $this->config = array_merge($this->config, $this->contentToArray($content, $filename));
                } else {
                    throw new Exception('Unable to open file');
                }
            } catch (Exception $e) {
                echo 'Skipping ' . $filename . ': ' . $e->getMessage();
            }
        }
    }

    /**
     * Parse file and return array
     */
    private function contentToArray(string $content, string $filename): array
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'json':
                $array = json_decode($content, true);
                break;
            /*
             * ...
             * case 'yaml':
             * ...
             */
            case 'default':
                throw new Exception('Unsupported file type: '.$ext);
                break;
        }

        if (is_null($array)) {
            throw new Exception('Unable to parse file');
        }

        return $array;
    }

    /**
     * Traverse the array to the dot path
     */
    private function getPath(string $path, array $data): bool
    {
        $found = true;
        $path = explode(".", $path);

        for ($i = 0; $i < count($path) && $found; $i++) {
            $key = $path[$i];

            if (array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                $found = false;
            }
        }

        if ($found) {
            $this->result = $data;
        }

        return $found;
    }

    /**
     * Return the leaf value or array
     *
     * @return string|array
     */
    public function getValue(string $path)
    {
        try {
            if ($this->getPath($path, $this->config)) {
                return $this->result;
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}
/*
$configParser = new ConfigParser();

$configParser->load('fixtures/config.json', 'config.invalid.json');
print_r($configParser->config);

$dotKey = 'database.host';
echo $dotKey . ": " . print_r($configParser->getValue($dotKey), 1)."\n";

$configParser->load('fixtures/config.local.json');
print_r($configParser->config);

$dotKey = 'cache';
echo $dotKey . ": " . print_r($configParser->getValue($dotKey), 1)."\n";
*/
