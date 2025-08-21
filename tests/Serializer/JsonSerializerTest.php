<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Serializer;

use PHPUnit\Framework\TestCase;
use Sulu\ApiClient\Serializer\JsonSerializer;
use Sulu\ApiClient\Serializer\SerializerInterface;

class JsonSerializerTest extends TestCase
{
    private JsonSerializer $serializer;

    protected function setUp(): void
    {
        $this->serializer = new JsonSerializer();
    }

    public function testImplementsSerializerInterface(): void
    {
        $this->assertInstanceOf(SerializerInterface::class, $this->serializer);
    }

    public function testConstructorWithDefaults(): void
    {
        $serializer = new JsonSerializer();
        $this->assertInstanceOf(JsonSerializer::class, $serializer);
    }

    public function testConstructorWithCustomFlags(): void
    {
        $serializer = new JsonSerializer(
            encodeFlags: JSON_PRETTY_PRINT,
            decodeFlags: JSON_THROW_ON_ERROR,
            depth: 256
        );
        $this->assertInstanceOf(JsonSerializer::class, $serializer);
    }

    public function testSerializeSimpleArray(): void
    {
        $data = ['name' => 'John', 'age' => 30];
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('{"name":"John","age":30}', $result);
    }

    public function testSerializeSimpleObject(): void
    {
        $data = (object) ['name' => 'John', 'age' => 30];
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('{"name":"John","age":30}', $result);
    }

    public function testSerializeNestedArray(): void
    {
        $data = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'city' => 'Paris',
                    'country' => 'France'
                ]
            ]
        ];
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('{"user":{"name":"John","address":{"city":"Paris","country":"France"}}}', $result);
    }

    public function testSerializeString(): void
    {
        $data = 'simple string';
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('"simple string"', $result);
    }

    public function testSerializeInteger(): void
    {
        $data = 42;
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('42', $result);
    }

    public function testSerializeFloat(): void
    {
        $data = 3.14;
        $result = $this->serializer->serialize($data);
        
        $this->assertSame('3.14', $result);
    }

    public function testSerializeBoolean(): void
    {
        $this->assertSame('true', $this->serializer->serialize(true));
        $this->assertSame('false', $this->serializer->serialize(false));
    }

    public function testSerializeNull(): void
    {
        $result = $this->serializer->serialize(null);
        
        $this->assertSame('null', $result);
    }

    public function testSerializeEmptyArray(): void
    {
        $result = $this->serializer->serialize([]);
        
        $this->assertSame('[]', $result);
    }

    public function testSerializeWithDefaultJsonFormat(): void
    {
        $data = ['test' => 'value'];
        $result = $this->serializer->serialize($data, 'json');
        
        $this->assertSame('{"test":"value"}', $result);
    }

    public function testSerializeWithUnsupportedFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only json format is supported');
        
        $this->serializer->serialize(['test' => 'value'], 'xml');
    }

    public function testSerializeWithPrettyPrintFlag(): void
    {
        $serializer = new JsonSerializer(encodeFlags: JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $data = ['name' => 'John', 'age' => 30];
        $result = $serializer->serialize($data);
        
        $this->assertStringContainsString("\n", $result);
        $this->assertStringContainsString('    ', $result); // indentation
    }

    public function testDeserializeSimpleArray(): void
    {
        $json = '{"name":"John","age":30}';
        $result = $this->serializer->deserialize($json);
        
        $this->assertSame(['name' => 'John', 'age' => 30], $result);
    }

    public function testDeserializeNestedArray(): void
    {
        $json = '{"user":{"name":"John","address":{"city":"Paris","country":"France"}}}';
        $result = $this->serializer->deserialize($json);
        
        $expected = [
            'user' => [
                'name' => 'John',
                'address' => [
                    'city' => 'Paris',
                    'country' => 'France'
                ]
            ]
        ];
        $this->assertSame($expected, $result);
    }

    public function testDeserializeString(): void
    {
        $json = '"simple string"';
        $result = $this->serializer->deserialize($json);
        
        $this->assertSame('simple string', $result);
    }

    public function testDeserializeInteger(): void
    {
        $json = '42';
        $result = $this->serializer->deserialize($json);
        
        $this->assertSame(42, $result);
    }

    public function testDeserializeFloat(): void
    {
        $json = '3.14';
        $result = $this->serializer->deserialize($json);
        
        $this->assertSame(3.14, $result);
    }

    public function testDeserializeBoolean(): void
    {
        $this->assertTrue($this->serializer->deserialize('true'));
        $this->assertFalse($this->serializer->deserialize('false'));
    }

    public function testDeserializeNull(): void
    {
        $result = $this->serializer->deserialize('null');
        
        $this->assertNull($result);
    }

    public function testDeserializeEmptyArray(): void
    {
        $result = $this->serializer->deserialize('[]');
        
        $this->assertSame([], $result);
    }

    public function testDeserializeEmptyPayload(): void
    {
        $result = $this->serializer->deserialize('');
        
        $this->assertNull($result);
    }

    public function testDeserializeWithDefaultJsonFormat(): void
    {
        $json = '{"test":"value"}';
        $result = $this->serializer->deserialize($json, 'json');
        
        $this->assertSame(['test' => 'value'], $result);
    }

    public function testDeserializeWithUnsupportedFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Only json format is supported');
        
        $this->serializer->deserialize('<xml></xml>', 'xml');
    }

    public function testDeserializeWithType(): void
    {
        $json = '{"name":"John","age":30}';
        $result = $this->serializer->deserialize($json, 'json', 'array');
        
        $this->assertSame(['name' => 'John', 'age' => 30], $result);
    }

    public function testDeserializeInvalidJson(): void
    {
        $this->expectException(\JsonException::class);
        
        $this->serializer->deserialize('{"invalid": json}');
    }

    public function testSerializeAndDeserializeRoundTrip(): void
    {
        $originalData = [
            'string' => 'test',
            'integer' => 123,
            'float' => 45.67,
            'boolean' => true,
            'null' => null,
            'array' => [1, 2, 3],
            'nested' => [
                'key' => 'value',
                'number' => 999
            ]
        ];
        
        $serialized = $this->serializer->serialize($originalData);
        $deserialized = $this->serializer->deserialize($serialized);
        
        $this->assertSame($originalData, $deserialized);
    }

    public function testDeserializeWithBigIntAsString(): void
    {
        $serializer = new JsonSerializer(decodeFlags: JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING);
        $bigIntJson = '9223372036854775808'; // Greater than PHP_INT_MAX, should be treated as string
        
        $result = $serializer->deserialize($bigIntJson);
        
        $this->assertSame('9223372036854775808', $result);
    }

    public function testCustomDepthLimit(): void
    {
        $serializer = new JsonSerializer(depth: 2);
        $deepData = ['level1' => ['level2' => ['level3' => 'too deep']]];
        
        $this->expectException(\JsonException::class);
        $serializer->serialize($deepData);
    }

    public function testSerializeWithNoThrowFlag(): void
    {
        $serializer = new JsonSerializer(encodeFlags: 0);
        
        // Create data that would cause JSON encoding to fail
        $invalidData = ["\xB1\x31"]; // invalid UTF-8
        
        $this->expectException(\JsonException::class);
        $serializer->serialize($invalidData);
    }

    public function testDeserializeWithNoThrowFlag(): void
    {
        $serializer = new JsonSerializer(decodeFlags: 0);
        
        $this->expectException(\JsonException::class);
        $serializer->deserialize('{"invalid": json}');
    }
}