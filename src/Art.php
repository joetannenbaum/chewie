<?php

declare(strict_types=1);

namespace Chewie;

class Art
{
    public static $dir = '';

    public static function setDirectory(string $dir): void
    {
        static::$dir = rtrim($dir, '/');
    }

    public static function get(string $name): string
    {
        $path = static::$dir === '' ? $name : static::$dir . '/' . $name;

        return file_get_contents($path . '.txt');
    }
}
