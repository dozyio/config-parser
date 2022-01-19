<?php

function loadFiles(...$filenames)
{
    $config = [];
    foreach($filenames as $filename) {
        try {
            if (file_exists($filename)){
                $content = file_get_contents($filename);
                if ($content === false) {
                    throw new Exception('Unable to read file ' . $filename);
                }

                $ext = pathinfo($filename, PATHINFO_EXTENSION);

                $config = array_merge(fileToArray($content, $ext));

            } else {
                throw new Exception('Unable to open file ' . $filename);
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            return null;
        }
    }

    return $config;
}

function fileToArray(string $content, $ext)
{
    echo "Parsing $ext file\n";

    $json = json_decode($content, true);
    if (is_null($json)) {
        throw new Exception('Invalid JSON');
    }

    return $json;
}

function getPath(string $path, array $data, ?mixed &$result): bool
{
    $found = true;
    $path = explode(".", $path);

    for ($i = 0; $i < count($path) && $found; $i++)
    {
        $key = $path[$i];

        if (array_key_exists($key, $data)){
            $data = $data[$key];
        } else {
            $found = false;
        }
    }

    if ($found) {
        $result = $data;
    }

    return $found;
}

function getValue(string $path, array $config)
{
    $result = null;

    try {
        if (getPath($path, $config, $result)) {
            return $result;
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}

$config = loadFiles('fixtures/config.json', 'fixtures/config.local.json');

if (!is_null($config)) {
    $dotKey = 'database.host';
    echo $dotKey . ": " . print_r(getValue($dotKey, $config), 1)."\n";
    $dotKey = 'cache';
    echo $dotKey . ": " . print_r(getValue($dotKey, $config), 1)."\n";
}
