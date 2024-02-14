<?php

namespace Chewie\Concerns;

use Throwable;

trait SetsUpAndResets
{
    public function setup($cb)
    {
        try {
            $this->capturePreviousNewLines();

            if (static::shouldFallback()) {
                return $this->fallback();
            }

            static::$interactive ??= stream_isatty(STDIN);

            if (!static::$interactive) {
                return $this->default();
            }

            try {
                static::terminal()->setTty('-icanon -isig -echo');
            } catch (Throwable $e) {
                static::output()->writeln("<comment>{$e->getMessage()}</comment>");
                static::fallbackWhen(true);

                return $this->fallback();
            }

            $this->hideCursor();

            try {

                $cb();
            } catch (Throwable $e) {
                $this->exitAltScreen();
                throw $e;
            }
        } finally {
            $this->clearListeners();
        }
    }
}
