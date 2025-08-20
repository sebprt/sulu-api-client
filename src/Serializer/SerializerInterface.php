<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Serializer;

interface SerializerInterface
{
    public function serialize(mixed $data, string $format = 'json'): string;

    /**
     * @template T
     *
     * @param class-string<T>|null $type
     *
     * @return T|mixed
     */
    public function deserialize(string $payload, string $format = 'json', ?string $type = null): mixed;
}
