<?php

namespace S\Halkode\Core\Abstracts\Services;

interface AccessTokenStorage
{
    public function has(string $name): bool;

    public function get(string $name): AccessToken;

    public function set(string $name, AccessToken $accessToken): void;
}