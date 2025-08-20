<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Fixtures;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

final class SimpleRequest implements RequestInterface
{
    private string $method;
    private $uri; // string|UriInterface
    /** @var array<string, list<string>> */
    private array $headers = [];
    private string $protocol = '1.1';
    private StreamInterface $body;
    private ?string $requestTarget = null;

    public function __construct(string $method, string $uri)
    {
        $this->method = strtoupper($method);
        $this->uri = new SimpleUri($uri);
        $this->body = new SimpleStream('');
    }

    public function getProtocolVersion(): string { return $this->protocol; }
    public function withProtocolVersion($version): self { $clone = clone $this; $clone->protocol = (string) $version; return $clone; }
    public function getHeaders(): array { return $this->headers; }
    public function hasHeader($name): bool { return isset($this->headers[strtolower($name)]); }
    public function getHeader($name): array { return $this->headers[strtolower($name)] ?? []; }
    public function getHeaderLine($name): string { return implode(', ', $this->getHeader($name)); }
    public function withHeader($name, $value): self
    {
        $clone = clone $this;
        $clone->headers[strtolower($name)] = is_array($value) ? array_values(array_map('strval', $value)) : [ (string) $value ];
        return $clone;
    }
    public function withAddedHeader($name, $value): self
    { $clone = clone $this; $lower = strtolower($name); $vals = is_array($value) ? $value : [$value]; foreach ($vals as $v) { $clone->headers[$lower][] = (string) $v; } return $clone; }
    public function withoutHeader($name): self
    { $clone = clone $this; unset($clone->headers[strtolower($name)]); return $clone; }
    public function getBody(): StreamInterface { return $this->body; }
    public function withBody(StreamInterface $body): self { $clone = clone $this; $clone->body = $body; return $clone; }
    public function getRequestTarget(): string { return $this->requestTarget ?? (string) $this->uri; }
    public function withRequestTarget($requestTarget): self { $clone = clone $this; $clone->requestTarget = (string) $requestTarget; return $clone; }
    public function getMethod(): string { return $this->method; }
    public function withMethod($method): self { $clone = clone $this; $clone->method = strtoupper((string) $method); return $clone; }
    public function getUri(): UriInterface { return $this->uri; }
    public function withUri(UriInterface $uri, $preserveHost = false): self { $clone = clone $this; $clone->uri = $uri; return $clone; }
}
