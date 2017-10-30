<?php
declare(strict_types=1);

namespace B2B\Factory\Tests;

use B2B\Factory\BaseFactory;

/**
 * Class TestFactory
 *
 * @package B2B\Factory\Tests
 */
class TestFactory extends BaseFactory
{
    protected $baseInterface = TestProviderContract::class;

    /**
     * @param string $name
     * @param array  ...$args
     *
     * @return TestProviderContract
     */
    public function resolve(string $name, ...$args): TestProviderContract
    {
        return parent::resolve($name, ...$args);
    }
}
