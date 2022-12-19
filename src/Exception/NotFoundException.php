<?php

declare(strict_types=1);

namespace Geekmusclay\DI\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    // Some code here ...
}
