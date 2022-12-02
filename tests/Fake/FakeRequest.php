<?php

namespace Geekmusclay\DI\Tests\Fake;

class FakeRequest
{
    private string $method;

    public function __constrcut(string $method = 'GET')
    {
        $this->method = $method;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function setMethod(string $method): self
    {
        $this->method = $method;

        return $this;
    }
}
