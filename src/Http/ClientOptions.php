<?php

namespace Sulu\ApiClient\Http;

/**
 * Configuration options for the HTTP client.
 */
class ClientOptions
{
    /**
     * @var int
     */
    private $timeout = 30;

    /**
     * @var bool
     */
    private $verifySsl = true;

    /**
     * Set the request timeout in seconds.
     *
     * @param int $timeout
     * @return $this
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;

        return $this;
    }

    /**
     * Get the request timeout in seconds.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set whether to verify SSL certificates.
     *
     * @param bool $verify
     * @return $this
     */
    public function setVerifySsl($verify)
    {
        $this->verifySsl = $verify;

        return $this;
    }

    /**
     * Get whether to verify SSL certificates.
     *
     * @return bool
     */
    public function getVerifySsl()
    {
        return $this->verifySsl;
    }
}