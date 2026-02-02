<?php

namespace S\Halkode\Core\Services;

use DateTimeInterface;
use S\Halkode\Core\Abstracts\Services\AccessToken;

final class SimpleAccessToken implements AccessToken
{
    private string $token;

    private DateTimeInterface $expirationAt;

    public function __construct(string $token, DateTimeInterface $expirationAt)
    {
        $this->token = $token;
        $this->expirationAt = $expirationAt;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getExpirationAt(): DateTimeInterface
    {
        return $this->expirationAt;
    }

    public function isExpirationPassed(string $timezone = 'UTC'): bool
    {
        $now = new \DateTimeImmutable('now', new \DateTimeZone($timezone));
        return $now >= $this->expirationAt;
    }
}