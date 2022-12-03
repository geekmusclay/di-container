<?php

declare(strict_types=1);

namespace Geekmusclay\DI\Core;

use Closure;
use Exception;
use Geekmusclay\DI\Excpetion\ContainerException;
use Geekmusclay\DI\Excpetion\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Describes the dependency injection container implementation
 */
class Container implements ContainerInterface
{
    /** @var array<string, mixed> $entries Array containing all registered entries */
    protected array $entries = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id         Identifier of the entry to look for.
     * @param array  $parameters Possible parameters to be passed to the input (optional)
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get(string $id, array $parameters = [])
    {
        // if we don't have it, just register it
        if (false === $this->has($id)) {
            $this->set($id);
        }

        return $this->resolve($this->entries[$id], $parameters);
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * Storing an entry manually
     *
     * @param string     $id    The identifier of the stored entry
     * @param mixed|null $entry Entry to store
     */
    public function set(string $id, $entry = null): self
    {
        if (null === $entry) {
            $entry = $id;
        }
        $this->entries[$id] = $entry;

        return $this;
    }

    /**
     * Resolve an entry and his dependencies
     *
     * @param string $entry The entry to resolve
     * @return mixed|object
     * @throws Exception
     */
    public function resolve(string $entry, array $parameters = [])
    {
        if ($entry instanceof Closure) {
            return $entry($this, $parameters);
        }
        $reflector = new ReflectionClass($entry);

        // check if class is instantiable
        if (false === $reflector->isInstantiable()) {
            throw new ContainerException("Class {$entry} is not instantiable");
        }

        // get class constructor
        $constructor = $reflector->getConstructor();
        if (null === $constructor) {
            // get new instance from class
            return $reflector->newInstance();
        }

        // get constructor params
        $parameters   = $constructor->getParameters();
        $dependencies = $this->getDependencies($parameters);

        // get the instance and store it
        $instance = $reflector->newInstanceArgs($dependencies);
        $this->set($entry, $instance);

        // get new instance with dependencies resolved
        return $instance;
    }

    /**
     * Get all entry dependencies
     *
     * @param array $parameters The entry needed parameters
     * @return array Resolved dependencies
     * @throws Exception
     */
    public function getDependencies(array $parameters): array
    {
        $dependencies = [];
        foreach ($parameters as $parameter) {
            // get the type hinted class
            $dependency = $parameter->getClass();
            if (null === $dependency) {
                // check if default value for a parameter is available
                if (true === $parameter->isDefaultValueAvailable()) {
                    // get default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new NotFoundException("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // get dependency resolved
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }
}
