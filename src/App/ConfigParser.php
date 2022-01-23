<?php

namespace App;

use Exception;

class ConfigParser
{
    /**
     * Holds the current configuation
     * @var array $config
     */
    public $config = [];

    /**
     * Errors for load file errors
     * @var array $errors
     */
    public $errors = [];

    /**
     * Load files and merge into config
     */
    public function load(string ...$filenames): bool
    {
        $this->errors = [];
        $numberOfFilesLoaded = 0;

        foreach ($filenames as $filename) {
            try {
                if (file_exists($filename)) {
                    $content = file_get_contents($filename);

                    if ($content === false) {
                        throw new Exception('Unable to read file');
                    }

                    $this->config = array_merge($this->config, $this->contentToArray($content, $filename));
                    $numberOfFilesLoaded++;
                } else {
                    throw new Exception('File does not exist');
                }
            } catch (Exception $e) {
                $this->errors[] = "Skipping $filename: ".$e->getMessage();
            }
        }
        return (bool) $numberOfFilesLoaded;
    }

    public function hasErrors(): bool
    {
        return (bool) count($this->errors);
    }

    /**
     * Parse content and return array
     *
     * @throws Exception
     */
    private function contentToArray(string $content, string $filename): array
    {
        $array = null;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        switch ($ext) {
            case 'json':
                // Simple json_decode here but could be a class per file type supported.
                // Class could be implementation of a Parser interface.
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
     *
     * Note PHP8 can return union type
     * @return ?string|array
     */
    private function getPath(string $path, array $data)
    {
        $found = true;
        $path = explode(".", $path);

        // Loop through path entries, replacing $data array with current path
        for ($i = 0; $i < count($path) && $found; $i++) {
            $key = $path[$i];

            if (array_key_exists($key, $data)) {
                $data = $data[$key];
            } else {
                $found = false;
            }
        }

        return $found ? $data : null;
    }

    /**
     * Return the value or array
     * Note PHP8 can return union type
     *
     * @return ?string|array
     */
    public function getValue(string $path)
    {
        return $this->getPath($path, $this->config);
    }
}
