<?php 

namespace Geekmusclay\DI\Tests\Fake;

use Psr\Container\ContainerInterface;

class FakeController
{
    private ContainerInterface $container;

    private FakeManager $manager;

    public function __construct(ContainerInterface $container, FakeManager $manager)
    {
        $this->container = $container;
        $this->manager = $manager;
    }

    public function getManager(): FakeManager
    {
        return $this->manager;
    }

    public function index()
    {
        return $this->manager->getMessage();
    }
}
