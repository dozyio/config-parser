<?php

namespace Test\Unit;

use App\ConfigParser;
use Exception;
use PHPUnit\Framework\TestCase;

class ConfigParserTest extends TestCase
{
    public function getPrivateMethod(string $className, string $methodName)
    {
        $reflector = new \ReflectionClass($className);
        $method = $reflector->getMethod($methodName);
        $method->setAccessible(true);
        return $method;
    }

    /** @test */
    public function can_parse_json_with_valid_content_and_valid_extension(): void
    {
        $parser = new ConfigParser();

        $testString = '{ "testKey": "value" }';
        $testFilename = 'test.json';
        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'contentToArray');

        $result = $contentToArrayMethod->invokeArgs($parser, [$testString, $testFilename]);

        $this->assertEquals(json_decode($testString, true), $result);
    }

    /** @test */
    public function can_not_parse_json_with_invalid_content_and_valid_extension(): void
    {
        $parser = new ConfigParser();

        $testString = 'test';
        $testFilename = 'test.json';
        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'contentToArray');

        $this->expectException(Exception::class);

        $contentToArrayMethod->invokeArgs($parser, [$testString, $testFilename]);
    }

    /** @test */
    public function can_not_parse_unsupported_extension(): void
    {
        $parser = new ConfigParser();

        $testString = 'test';
        $testFilename = 'test.yaml';
        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'contentToArray');

        $this->expectException(Exception::class);

        $contentToArrayMethod->invokeArgs($parser, [$testString, $testFilename]);
    }

    /** @test */
    public function can_get_value_from_path(): void
    {
        $parser = new ConfigParser();

        $testPath = 'test';
        $testValue = 1;
        $testArray = ['test' => $testValue];

        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'getPath');

        $result = $contentToArrayMethod->invokeArgs($parser, [$testPath, $testArray]);
        $this->assertEquals($testValue, $result);
    }

    /** @test */
    public function can_get_array_from_path(): void
    {
        $parser = new ConfigParser();

        $testPath = 'test';
        $testSubArray = ['arr' => 'test'];
        $testArray = ['test' => $testSubArray];
        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'getPath');

        $result = $contentToArrayMethod->invokeArgs($parser, [$testPath, $testArray]);
        $this->assertEquals($testSubArray, $result);
    }

    /** @test */
    public function invalid_path_returns_null(): void
    {
        $parser = new ConfigParser();

        $testPath = 'invalid';
        $testArray = ['test' => 1];
        $contentToArrayMethod = $this->getPrivateMethod(ConfigParser::class, 'getPath');

        $result = $contentToArrayMethod->invokeArgs($parser, [$testPath, $testArray]);
        $this->assertEquals(null, $result);
    }
}
