<?php

namespace LaminasTest\ApiTools\HttpCache;

use PHPUnit\Framework\Assert;
use ReflectionProperty;

trait DeprecatedAssertionsTrait
{
    /**
     * @param mixed $expected
     * @param object $actualClassOrObject
     */
    public static function assertAttributeSame(
        $expected,
        string $actualAttributeName,
        $actualClassOrObject,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($actualClassOrObject, $actualAttributeName);
        $r->setAccessible(true);

        Assert::assertSame($expected, $r->getValue($actualClassOrObject), $message);
    }

    /**
     * @param object $classOrObject
     */
    public static function assertAttributeInstanceOf(
        string $expected,
        string $attributeName,
        $classOrObject,
        string $message = ''
    ): void {
        $r = new ReflectionProperty($classOrObject, $attributeName);
        $r->setAccessible(true);

        Assert::assertInstanceOf($expected, $r->getValue($classOrObject), $message);
    }
}
