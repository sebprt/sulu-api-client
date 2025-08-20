<?php

declare(strict_types=1);

namespace Sulu\ApiClient\Tests\Fixtures;

use Psr\Http\Message\StreamInterface;

final class SimpleStream implements StreamInterface
{
    private string $content;
    private bool $seekable = true;
    private bool $readable = true;
    private bool $writable = true;
    private int $position = 0;

    public function __construct(string $content = '')
    {
        $this->content = $content;
    }

    public function __toString(): string { return $this->content; }
    public function close(): void {}
    public function detach() { return null; }
    public function getSize(): ?int { return strlen($this->content); }
    public function tell(): int { return $this->position; }
    public function eof(): bool { return $this->position >= strlen($this->content); }
    public function isSeekable(): bool { return $this->seekable; }
    public function seek($offset, $whence = SEEK_SET): void
    {
        if (!$this->seekable) { throw new \RuntimeException('Not seekable'); }
        $len = strlen($this->content);
        if ($whence === SEEK_SET) { $this->position = (int) $offset; }
        elseif ($whence === SEEK_CUR) { $this->position += (int) $offset; }
        elseif ($whence === SEEK_END) { $this->position = $len + (int) $offset; }
        $this->position = max(0, min($this->position, $len));
    }
    public function rewind(): void { $this->position = 0; }
    public function isWritable(): bool { return $this->writable; }
    public function write($string): int
    {
        if (!$this->writable) { throw new \RuntimeException('Not writable'); }
        $this->content .= (string) $string;
        $this->position = strlen($this->content);
        return strlen((string) $string);
    }
    public function isReadable(): bool { return $this->readable; }
    public function read($length): string
    {
        if (!$this->readable) { throw new \RuntimeException('Not readable'); }
        $chunk = substr($this->content, $this->position, $length);
        $this->position += strlen($chunk);
        return $chunk;
    }
    public function getContents(): string { return substr($this->content, $this->position); }
    public function getMetadata($key = null): mixed { return null; }
}
