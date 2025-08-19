<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Serializer;

final class JsonSerializer implements SerializerInterface
{
    public function serialize(mixed $data, string $format = 'json'): string
    {
        if ($format !== 'json') {
            throw new \InvalidArgumentException('Only json format is supported');
        }
        $json = json_encode($data, JSON_THROW_ON_ERROR);
        return $json;
    }

    public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed
    {
        if ($format !== 'json') {
            throw new \InvalidArgumentException('Only json format is supported');
        }
        $decoded = $payload === '' ? null : json_decode($payload, true, 512, JSON_THROW_ON_ERROR);
        // Simple pass-through until Symfony Serializer is wired
        return $decoded;
    }
}
