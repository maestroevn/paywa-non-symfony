<?php

declare(strict_types=1);

namespace Paywa\CommissionTask\CommonCore;

use Exception;

class Singleton
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    protected static array $instances = [];

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    final protected function __construct()
    {
    }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone()
    {
    }

    /**
     * Singletons should not be restore-able from strings.
     * @throws Exception
     */
    public function __wakeup()
    {
        throw new Exception('Cannot un-serialize a singleton.');
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public static function getInstance(): Singleton
    {
        $className = static::class;
        if (false === array_key_exists($className, self::$instances)) {
            self::$instances[$className] = new static();
        }

        return self::$instances[$className];
    }
}
