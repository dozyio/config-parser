<?php

namespace Test\Feature;

use App\ConfigParser;
use PHPUnit\Framework\TestCase;

class ConfigParserTest extends TestCase
{
    /** @test */
    public function can_load_json_config(): void
    {
        $testFile = 'tests/fixtures/config.json';

        $parser = new ConfigParser();
        $parser->load($testFile);
        $this->assertEquals(json_decode(file_get_contents($testFile), true), $parser->config);
    }

    /** @test */
    public function can_not_load_missing_json_config(): void
    {
        $testFile = 'tests/fixtures/config.json-missing';

        $parser = new ConfigParser();
        $result = $parser->load($testFile);
        $this->assertEquals(false, $result);
    }

    /** @test */
    public function can_not_load_invalid_json_config(): void
    {
        $testFile = 'tests/fixtures/config.invalid.json';

        $parser = new ConfigParser();
        $result = $parser->load($testFile);
        $this->assertEquals(false, $result);
    }

    /** @test */
    public function can_load_multiple_json_configs(): void
    {
        $testFile1 = 'tests/fixtures/config.json';
        $testFile2 = 'tests/fixtures/config.local.json';

        $parser = new ConfigParser();
        $result = $parser->load($testFile1, $testFile2);
        $this->assertEquals(true, $result);
    }

    /** @test */
    public function loading_invalid_json_config_sets_error(): void
    {
        $testFile = 'tests/fixtures/config.invalid.json';

        $parser = new ConfigParser();
        $result = $parser->load($testFile);
        $this->assertEquals(false, $result);

        $result = $parser->hasErrors();
        $this->assertEquals(true, $result);
    }

    /** @test */
    public function can_load_multiple_json_configs_including_invalid(): void
    {
        $testFile1 = 'tests/fixtures/config.json';
        $testFile2 = 'tests/fixtures/config.invalid.json';
        $testFile3 = 'tests/fixtures/config.local.json';

        $parser = new ConfigParser();
        $result = $parser->load($testFile1, $testFile2, $testFile3);
        $this->assertEquals(true, $result);

        $result = $parser->hasErrors();
        $this->assertEquals(true, $result);
    }

    /** @test */
    public function can_get_value_from_config(): void
    {
        $testFile1 = 'tests/fixtures/config.json';
        $expected = 'mysql';

        $parser = new ConfigParser();
        $result = $parser->load($testFile1);
        $this->assertEquals(true, $result);

        $result = $parser->getValue('database.host');
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function can_get_array_from_config(): void
    {
        $testFile1 = 'tests/fixtures/config.json';

        $expected = ["redis" =>
            ["host" => "redis", "port" => 6379]
        ];

        $parser = new ConfigParser();
        $result = $parser->load($testFile1);
        $this->assertEquals(true, $result);

        $result = $parser->getValue('cache');
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function can_get_value_from_second_config(): void
    {
        $testFile1 = 'tests/fixtures/config.json';
        $testFile2 = 'tests/fixtures/config.local.json';

        $expected = '127.0.0.1';

        $parser = new ConfigParser();
        $result = $parser->load($testFile1, $testFile2);
        $this->assertEquals(true, $result);

        $result = $parser->getValue('database.host');
        $this->assertEquals($expected, $result);
    }

    /** @test */
    public function can_get_array_from_second_config(): void
    {
        $testFile1 = 'tests/fixtures/config.json';
        $testFile2 = 'tests/fixtures/config.local.json';

        $expected = ["redis" =>
            ["host" => "127.0.0.1", "port" => 6379]
        ];

        $parser = new ConfigParser();
        $result = $parser->load($testFile1, $testFile2);
        $this->assertEquals(true, $result);

        $result = $parser->getValue('cache');
        $this->assertEquals($expected, $result);
    }
}
