<?php

declare(strict_types=1);

namespace Chewie;

class Theme
{
    public static $namespace = 'App\\Prompts\\Renderers';

    public static function setNamespace(string $namespace): void
    {
        static::$namespace = rtrim($namespace, '\\');
    }
}
