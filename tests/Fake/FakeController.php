<?php 

namespace Geekmusclay\DI\Tests\Fake;

class FakeController
{
    private FakeManager $manager;

    public function __construct(FakeManager $manager)
    {
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
