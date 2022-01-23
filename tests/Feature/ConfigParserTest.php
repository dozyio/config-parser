<?php

namespace Test\Feature;

use App\ConfigParser;
use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\assertEquals;

class ConfigParserTest extends TestCase
{
    /** @test */
    public function can_load_json_config(): void
    {
        $testFile = 'tests/fixtures/config.json';

        $parser = new ConfigParser();
        $parser->load($testFile);
        assertEquals(json_decode(file_get_contents($testFile), true), $parser->config);
    }
}
