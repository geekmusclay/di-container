<?php 

namespace Geekmusclay\DI\Tests\Fake;

class FakeManager
{
    private FakeRepository $repository;

    public function __construct(FakeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getRepository(): FakeRepository
    {
        return $this->repository;
    }

    public function getMessage()
    {
        return $this->repository->getMessage();
    }
}
