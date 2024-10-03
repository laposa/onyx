<?php
/**
 * Copyright (c) 2005-2020 Laposa Limited (https://laposa.ie)
 * Licensed under the New BSD License. See the file LICENSE.txt for details.
 *
 */

use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Wrapper class for dependency injection container
 */
class Onyx_Container {
    /** @var Onyx_Container */
    private static $instance;
    /** @var ContainerBuilder */
    private $container;

    /**
     * Prevent from creating the class manually
     */
    /*
     * disabled as it's showing warning in PHP8
     * Warning: The magic method Onyx_Container::__wakeup() must have public visibility in /var/www/vendor/laposa/onyx/lib/onyx.container.php on line 24
     *
    protected function __construct() { }
    protected function __clone() { }
    protected function __wakeup() { }
    */

    /**
     * Onyx_Container singleton constructor
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
            self::$instance->container = new ContainerBuilder();
        }

        return self::$instance;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function set(string $key, $value)
    {
        $value = $this->wrapPrimitive($value);
        $this->container->set($key, $value);
        return $this;
    }

    /**
     * @param string $key
     * @param $value
     * @param string $separator
     * @return $this
     */
    public function append(string $key, $value, $separator = '')
    {
        if ($this->has($key)) {
            $this->set($key, $this->get($key) . $separator . $value);
        } else {
            $this->set($key, $value);
        }

        return $this;
    }

    /**
     * @param string $key
     * @return object|null
     */
    public function get(string $key)
    {
        try {
            return $this->unwrapPrimitive($this->container->get($key));
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get all registered services as associative array
     * @return array
     */
    public function getServices()
    {
        $resources = [];
        foreach ($this->container->getServiceIds() as $key) {
            $resources[$key] = $this->get($key);
        }

        return $resources;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key)
    {
        return $this->container->has($key);
    }

    /**
     * @return ContainerBuilder
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Wrap primitive variable types, so it can be used within Symfony dependency container
     *
     * @param $value
     * @return stdClass
     */
    protected function wrapPrimitive($value)
    {
        if (is_object($value)) return $value;

        $wrappedValue = new stdClass();
        $wrappedValue->value = $value;
        $wrappedValue->_isWrapped = true;
        return $wrappedValue;
    }

    /**
     * Unwrap primitive variable obtained from the dependency container
     *
     * @param $value
     * @return mixed
     */
    protected function unwrapPrimitive($value)
    {
        if ($value instanceof stdClass && isset($value->_isWrapped)) {
            return $value->value;
        }

        return $value;
    }
}
