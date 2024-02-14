<?php

namespace Chewie\Concerns;

use Chewie\Theme;

trait RegistersThemes
{
    public function registerTheme(?string $theme = null): void
    {
        $class = basename(str_replace('\\', '/', static::class));

        $theme ??= Theme::$namespace . '\\' . $class . 'Renderer';

        static::$themes['default'][static::class] = $theme;
    }
}
