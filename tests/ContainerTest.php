<?php

namespace Geekmusclay\DI\Tests;

use Geekmusclay\DI\Core\Container;
use Geekmusclay\DI\Exception\NotFoundException;
use Geekmusclay\DI\Exception\ContainerException;
use Geekmusclay\DI\Tests\Fake\FakeComplexConstructor;
use Geekmusclay\DI\Tests\Fake\FakeController;
use Geekmusclay\DI\Tests\Fake\FakeInterface;
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

    public function testComplexConstructor()
    {
        $test = $this->container->get(FakeComplexConstructor::class, [
            $this->container->get(FakeManager::class),
            'coucou'
        ]);
        $this->assertInstanceOf(FakeComplexConstructor::class, $test);
        $this->assertInstanceOf(FakeManager::class, $test->getManager());
        $this->assertEquals('coucou', $test->getName());
    }

    public function testBulk()
    {
        $this->container->flush();
        $this->container->bulk([
            FakeController::class => [],
            FakeComplexConstructor::class => [
                $this->container->get(FakeManager::class),
                'coucou'
            ]
        ]);

        $controller = $this->container->get(FakeController::class);
        $this->assertInstanceOf(FakeController::class, $controller);
        $this->assertEquals('Hello World', $controller->index());

        $manager = $controller->getManager();
        $this->assertInstanceOf(FakeManager::class, $manager);

        $repository = $manager->getRepository();
        $this->assertInstanceOf(FakeRepository::class, $repository);

        $test = $this->container->get(FakeComplexConstructor::class);
        $this->assertInstanceOf(FakeComplexConstructor::class, $test);
        $this->assertInstanceOf(FakeManager::class, $test->getManager());
        $this->assertEquals('coucou', $test->getName());
    }

    public function testNotFoundException()
    {
        $this->expectException(NotFoundException::class);
        $this->container->flush();
        $this->container->get(FakeComplexConstructor::class);
    }

    public function testContainerException()
    {
        $this->expectException(ContainerException::class);
        $this->container->flush();
        $this->container->get(FakeInterface::class);
    }
}
