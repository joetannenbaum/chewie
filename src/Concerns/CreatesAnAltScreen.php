<?php

namespace Chewie\Concerns;

trait CreatesAnAltScreen
{
    public function createAltScreen()
    {
        if (getenv('NO_ALT_SCREEN')) {
            return;
        }

        // tput smcup
        static::output()->write("\e[?1049h");
    }

    public function exitAltScreen()
    {
        if (getenv('NO_ALT_SCREEN')) {
            return;
        }

        // tput rmcup
        static::output()->write("\e[?1049l");
    }

    public function __destruct()
    {
        parent::__destruct();
        $this->exitAltScreen();
    }
}
