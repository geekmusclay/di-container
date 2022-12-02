<?php

declare(strict_types=1);

namespace Geekmusclay\DI\Core;

use Closure;
use Exception;
use ReflectionClass;
use Psr\Container\ContainerInterface;

/**
 * Describes the dependency injection container implementation
 */
class Container implements ContainerInterface
{
    /**
     * @var array $entries Array containing all registered entries
     */
    protected array $entries = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
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
     *
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * @param      $id
     * @param null $concrete
     */
    public function set(string $id, $concrete = null): self
    {
        if (null === $concrete) {
            $concrete = $id;
        }
        $this->entries[$id] = $concrete;

        return $this;
    }

    /**
     * resolve single
     *
     * @param string $concrete
     *
     * @return mixed|object
     * @throws Exception
     */
    public function resolve($concrete, array $parameters = [])
    {
        if ($concrete instanceof Closure) {
            return $concrete($this, $parameters);
        }
        $reflector = new ReflectionClass($concrete);
        
        // check if class is instantiable
        if (!$reflector->isInstantiable()) {
            throw new Exception("Class {$concrete} is not instantiable");
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

        // get new instance with dependencies resolved
        return $reflector->newInstanceArgs($dependencies);
    }

    /**
     * get all dependencies resolved
     *
     * @param $parameters
     *
     * @return array
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
                if ($parameter->isDefaultValueAvailable()) {
                    // get default value of parameter
                    $dependencies[] = $parameter->getDefaultValue();
                } else {
                    throw new Exception("Can not resolve class dependency {$parameter->name}");
                }
            } else {
                // get dependency resolved
                $dependencies[] = $this->get($dependency->name);
            }
        }

        return $dependencies;
    }
}