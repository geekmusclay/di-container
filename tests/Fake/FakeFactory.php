<?php 

namespace Geekmusclay\DI\Tests\Fake;

use Psr\Container\ContainerInterface;

class FakeFactory
{
    public function __invoke(string $name, ContainerInterface $container)
    {
        $manager = $container->get(FakeManager::class);
        return new FakeController($container, $manager);
    }
}
