<?php

namespace Sunder\Http\Request;

readonly class Request
{
    public function __construct(
        public string $method,
        public string $uri,
        public array  $data = []
    )
    {

    }
}