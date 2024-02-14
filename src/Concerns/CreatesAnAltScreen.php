<?php

namespace Chewie\Concerns;

trait CreatesAnAltScreen
{
    public function createAltScreen()
    {
        if (env('NO_ALT_SCREEN')) {
            return;
        }

        // tput smcup
        static::output()->write("\e[?1049h");
    }

    public function exitAltScreen()
    {
        if (env('NO_ALT_SCREEN')) {
            return;
        }

        // tput rmcup
        static::output()->write("\e[?1049l");
    }
}
