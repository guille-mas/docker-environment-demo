<?php

namespace Befeni\Utils;

/**
 * To be used by any class that must be a Singleton
 */
trait SingletonTrait {
    private static ?self $instance = null;

    private function __construct(){}

    public static function getInstance(): self
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    protected function __clone() { }
    protected function __sleep() { }
    protected function __wakeup() { }
}