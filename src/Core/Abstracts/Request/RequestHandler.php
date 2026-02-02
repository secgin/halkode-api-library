<?php

namespace S\Halkode\Core\Abstracts\Request;

use S\Halkode\Core\Abstracts\Result\Result as ResultInterface;

interface RequestHandler
{
    public function handle(Request $request): ResultInterface;
}