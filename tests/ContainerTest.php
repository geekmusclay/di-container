<?php

namespace Geekmusclay\DI\Tests;

use Geekmusclay\DI\Core\Container;
use Geekmusclay\DI\Tests\Fake\FakeController;
use Geekmusclay\DI\Tests\Fake\FakeManager;
use Geekmusclay\DI\Tests\Fake\FakeRepository;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    private Container $container;

    public function setUp(): void
    {
        $this->container = new Container();
    }

    public function testDependencyInjection()
    {
        $controller = $this->container->get(FakeController::class);
        $this->assertInstanceOf(FakeController::class, $controller);

        $manager = $controller->getManager();
        $this->assertInstanceOf(FakeManager::class, $manager);

        $repository = $manager->getRepository();
        $this->assertInstanceOf(FakeRepository::class, $repository);

        $this->assertEquals('Hello World', $controller->index());
    }
}
