<?php
declare(strict_types=1);

namespace B2B\Factory;

use B2B\Factory\Exceptions\ProviderAlreadyExistsException;
use B2B\Factory\Exceptions\ProviderNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;

/**
 * Class SimpleFactory
 *
 * @package B2B\TCA\Core\Support
 */
abstract class BaseFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $baseInterface;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * Factory constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    /**
     * Get wizards names.
     *
     * @return array
     */
    public function all(): array
    {
        return array_keys($this->providers);
    }

    /**
     * @param string               $name
     * @param callable|string|null $class
     *
     * @return static
     * @throws ProviderNotFoundException
     * @throws ProviderAlreadyExistsException
     */
    public function extend(string $name, $class = null): self
    {
        //Checking for the existence of the provider with the same name
        assert(
            !array_key_exists($name, $this->providers),
            new ProviderAlreadyExistsException("Provider with name '{$name}' already exists")
        );

        if (!is_callable($class)) {
            if ($class === null) {
                $class = $name;
            }
            //Checking for the existence of a class
            assert(
                $this->baseInterface === null || is_subclass_of($class, $this->baseInterface, true),
                new ProviderNotFoundException("Provider with name '{$name}' must be implement {$this->baseInterface}")
            );
        }

        $this->providers[$name] = $class;

        return $this;
    }

    /**
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     * @throws ProviderNotFoundException
     */
    public function resolve(string $name, ...$args)
    {
        $className = $this->providers[$name] ?? $name;

        array_unshift($args, $this->container);

        if (is_callable($className)) {
            $provider = call_user_func_array($className, $args);

            assert(
                $this->baseInterface === null
                || (is_object($provider) && $provider instanceof $this->baseInterface),
                new ProviderNotFoundException("Provider with name '{$name}' must be implement {$this->baseInterface}")
            );

            return $provider;
        }

        assert(
            class_exists($className),
            new ProviderNotFoundException("Provider with class '{$className}' not found.")
        );

        $class = new ReflectionClass($className);

        assert(
            $this->baseInterface === null || $class->implementsInterface($this->baseInterface),
            new ProviderNotFoundException("Provider with name '{$name}' must be implement {$this->baseInterface}")
        );

        return $class->hasMethod('__construct') ? $class->newInstanceArgs($args) : $class->newInstance();
    }
}
