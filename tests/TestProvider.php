<?php
declare(strict_types=1);

namespace B2B\Factory\Tests;

use Psr\Container\ContainerInterface;

/**
 * Class TestProvider
 *
 * @package B2B\Factory\Tests
 */
class TestProvider implements TestProviderContract
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var int
     */
    protected $value;

    /**
     * TestProvider constructor.
     *
     * @param ContainerInterface $container
     * @param int                $value
     */
    public function __construct(ContainerInterface $container, int $value = 0)
    {
        $this->container = $container;
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }
}
