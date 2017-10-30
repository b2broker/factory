<?php
declare(strict_types=1);

namespace B2B\Factory\Tests;

use B2B\Factory\BaseFactory;
use League\Container\Container;
use PHPUnit\Framework\TestCase;

/**
 * Class FactoryTest
 *
 * @package B2B\Factory\Tests
 * @covers  B2B\Factory\BaseFactory
 */
class FactoryTest extends TestCase
{
    public static function testCreateFactory(): void
    {
        self::assertInstanceOf(
            BaseFactory::class,
            new TestFactory(new Container())
        );
    }

    public function testExtendClassFactory(): void
    {
        $factory = new TestFactory(new Container());

        self::assertEquals($factory, $factory->extend('test', TestProvider::class));

        self::assertInstanceOf(TestProvider::class, $factory->resolve('test'));

        self::assertEquals(42, $factory->resolve('test', 42)->getValue());
    }

    /**
     * @expectedException \B2B\Factory\Exceptions\ProviderAlreadyExistsException
     */
    public function testExtendSameNameFactory(): void
    {
        $factory = new TestFactory(new Container());
        $factory->extend('test', TestProvider::class);
        $factory->extend('test', TestProvider::class);
    }

    /**
     * @expectedException \B2B\Factory\Exceptions\ProviderNotFoundException
     */
    public function testExtendNotProvider(): void
    {
        $factory = new TestFactory(new Container());
        $factory->extend('test', Container::class);
    }

    public function testExtendCallable(): void
    {
        $factory = new TestFactory(new Container());
        $factory->extend('test', function ($container) {
            return new TestProvider($container);
        });

        self::assertInstanceOf(TestProvider::class, $factory->resolve('test'));
    }

    public function testExtendCallableArgs(): void
    {
        $factory = new TestFactory(new Container());
        $factory->extend('test', function ($container, int $a) {
            static::assertInstanceOf(Container::class, $container);
            static::assertEquals(35, $a);

            return new TestProvider($container);
        });

        self::assertInstanceOf(TestProvider::class, $factory->resolve('test', 35));
    }

    /**
     * @expectedException \B2B\Factory\Exceptions\ProviderNotFoundException
     */
    public function testExtendCallableNotProvider(): void
    {
        $factory = new TestFactory(new Container());
        $factory
            ->extend('test', function () {
                return new Container();
            })
            ->resolve('test');
    }
}
