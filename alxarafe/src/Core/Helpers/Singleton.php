<?php
/**
 * Alxarafe. Development of PHP applications in a flash!
 * Copyright (C) 2018 Alxarafe <info@alxarafe.com>
 */

namespace Alxarafe\Helpers;

trait Singleton
{
    private static $instance;

    private final function __construct()
    {
    }

    public final static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private final function __clone()
    {
    }

    private final function __wakeup()
    {
    }
}