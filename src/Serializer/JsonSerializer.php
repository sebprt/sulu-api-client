<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Serializer;

final class JsonSerializer implements SerializerInterface
{
    public function __construct(
        private readonly int $encodeFlags = JSON_THROW_ON_ERROR,
        private readonly int $decodeFlags = JSON_THROW_ON_ERROR | JSON_BIGINT_AS_STRING,
        private readonly int $depth = 512,
    ) {
    }

    public function serialize(mixed $data, string $format = 'json'): string
    {
        if ('json' !== $format) {
            throw new \InvalidArgumentException('Only json format is supported');
        }
        $json = json_encode($data, $this->encodeFlags, max(1, $this->depth));
        if (($this->encodeFlags & JSON_THROW_ON_ERROR) === 0) {
            $err = json_last_error();
            if (JSON_ERROR_NONE !== $err) {
                throw new \JsonException(json_last_error_msg(), $err);
            }
        }

        return (string) $json;
    }

    public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed
    {
        if ('json' !== $format) {
            throw new \InvalidArgumentException('Only json format is supported');
        }
        if ('' === $payload) {
            return null;
        }
        $decoded = json_decode($payload, true, max(1, $this->depth), $this->decodeFlags);
        if (($this->decodeFlags & JSON_THROW_ON_ERROR) === 0) {
            $err = json_last_error();
            if (JSON_ERROR_NONE !== $err) {
                throw new \JsonException(json_last_error_msg(), $err);
            }
        }

        return $decoded;
    }
}
