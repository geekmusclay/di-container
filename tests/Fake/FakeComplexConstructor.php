<?php 

namespace Geekmusclay\DI\Tests\Fake;

class FakeComplexConstructor
{
    private FakeManager $manager;

    private string $name;

    public function __construct(FakeManager $manager, string $name)
    {
        $this->manager = $manager;
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getManager(): FakeManager
    {
        return $this->manager;
    }
}