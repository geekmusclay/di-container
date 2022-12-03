<?php

declare(strict_types=1);

namespace Geekmusclay\DI\Excpetion;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    // Some code here ...
}
