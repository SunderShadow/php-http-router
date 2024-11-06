<?php

namespace Sunder\Http\Request;

readonly class Response
{
    public function __construct(
        public array  $headers = [],
        public int    $status  = 0,
        public string $body    = ''
    )
    {
    }
}