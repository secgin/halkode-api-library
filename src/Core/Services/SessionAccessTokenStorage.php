<?php

namespace S\Halkode\Core\Services;

use DateTimeImmutable;
use DateTimeInterface;
use S\Halkode\Core\Abstracts\Services\AccessToken;
use S\Halkode\Core\Abstracts\Services\AccessTokenStorage;

final class SessionAccessTokenStorage implements AccessTokenStorage
{
    private const SESSION_KEY = '_access_tokens';

    public function __construct()
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }
    }

    public function has(string $name): bool
    {
        return isset($_SESSION[self::SESSION_KEY][$name]);
    }

    public function get(string $name): AccessToken
    {
        if (!$this->has($name))
            return new SimpleAccessToken('', new DateTimeImmutable());

        $data = $_SESSION[self::SESSION_KEY][$name];

        try
        {
            return new SimpleAccessToken(
                $data['token'],
                new DateTimeImmutable($data['expiration_at'])
            );
        }
        catch (\Exception $ex)
        {
            return new SimpleAccessToken('', new DateTimeImmutable());
        }
    }

    public function set(string $name, AccessToken $accessToken): void
    {
        $_SESSION[self::SESSION_KEY][$name] = [
            'token' => $accessToken->getToken(),
            'expiration_at' => $accessToken->getExpirationAt()->format(DateTimeInterface::ATOM)
        ];
    }
}