<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Fixtures;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class SimpleResponse implements ResponseInterface
{
    private string $reasonPhrase = '';
    /** @var array<string, list<string>> */
    private array $headers = [];
    private string $protocol = '1.1';
    private StreamInterface $body;

    /**
     * @param array<string, string|string[]> $headers
     */
    public function __construct(private int $statusCode = 200, array $headers = [], string $body = '')
    {
        foreach ($headers as $name => $value) {
            $this->headers[strtolower($name)] = is_array($value) ? array_map('strval', $value) : [ (string) $value ];
        }
        $this->body = new SimpleStream($body);
    }

    public function getProtocolVersion(): string { return $this->protocol; }
    public function withProtocolVersion($version): self { $clone = clone $this; $clone->protocol = (string) $version; return $clone; }
    public function getHeaders(): array { return $this->headers; }
    public function hasHeader($name): bool { return isset($this->headers[strtolower($name)]); }
    public function getHeader($name): array { return $this->headers[strtolower($name)] ?? []; }
    public function getHeaderLine($name): string { return implode(', ', $this->getHeader($name)); }
    public function withHeader($name, $value): self { $clone = clone $this; $clone->headers[strtolower($name)] = is_array($value) ? array_map('strval', $value) : [ (string) $value ]; return $clone; }
    public function withAddedHeader($name, $value): self { $clone = clone $this; $lower = strtolower($name); $vals = is_array($value) ? $value : [$value]; foreach ($vals as $v) { $clone->headers[$lower][] = (string) $v; } return $clone; }
    public function withoutHeader($name): self { $clone = clone $this; unset($clone->headers[strtolower($name)]); return $clone; }
    public function getBody(): StreamInterface { return $this->body; }
    public function withBody(StreamInterface $body): self { $clone = clone $this; $clone->body = $body; return $clone; }
    public function getStatusCode(): int { return $this->statusCode; }
    public function withStatus($code, $reasonPhrase = ''): self { $clone = clone $this; $clone->statusCode = (int) $code; $clone->reasonPhrase = (string) $reasonPhrase; return $clone; }
    public function getReasonPhrase(): string { return $this->reasonPhrase; }
}
