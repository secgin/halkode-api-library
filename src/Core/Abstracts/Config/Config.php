<?php

namespace S\Halkode\Core\Abstracts\Config;

interface Config
{
    public function get(string $key): string;

    public function set(string $key, $value);
}