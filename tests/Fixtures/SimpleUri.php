<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Fixtures;

use Psr\Http\Message\UriInterface;

final class SimpleUri implements UriInterface
{
    private string $uri;

    public function __construct(string $uri)
    {
        $this->uri = $uri;
    }

    public function __toString(): string { return $this->uri; }
    public function getScheme(): string { return parse_url($this->uri, PHP_URL_SCHEME) ?? ''; }
    public function getAuthority(): string { return ''; }
    public function getUserInfo(): string { return ''; }
    public function getHost(): string { return parse_url($this->uri, PHP_URL_HOST) ?? ''; }
    public function getPort(): ?int { return parse_url($this->uri, PHP_URL_PORT) ?: null; }
    public function getPath(): string { return parse_url($this->uri, PHP_URL_PATH) ?? ''; }
    public function getQuery(): string { return parse_url($this->uri, PHP_URL_QUERY) ?? ''; }
    public function getFragment(): string { return parse_url($this->uri, PHP_URL_FRAGMENT) ?? ''; }
    public function withScheme($scheme): self { $clone = clone $this; return $clone; }
    public function withUserInfo($user, $password = null): self { $clone = clone $this; return $clone; }
    public function withHost($host): self { $clone = clone $this; return $clone; }
    public function withPort($port): self { $clone = clone $this; return $clone; }
    public function withPath($path): self { $clone = clone $this; return $clone; }
    public function withQuery($query): self { $clone = clone $this; return $clone; }
    public function withFragment($fragment): self { $clone = clone $this; return $clone; }
}
