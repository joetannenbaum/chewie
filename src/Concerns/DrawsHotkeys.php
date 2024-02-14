<?php

namespace Chewie\Concerns;

use Chewie\Output\Util;

trait DrawsHotkeys
{
    protected array $hotkeys = [];

    protected function hotkey(string $key, string $label, bool $active = true)
    {
        $key = $active ? $key : $this->dim($key);

        $this->hotkeys[] = $key . ' ' . $this->dim($label);
    }

    protected function hotkeyQuit()
    {
        $this->hotkey('q', 'Quit');
    }

    protected function clearHotkeys()
    {
        $this->hotkeys = [];
    }

    protected function hotkeySpacing()
    {
        return str_repeat(' ', 4);
    }

    protected function hotkeys(): array
    {
        $width = $this->prompt->terminal()->cols();

        $hotkeyLines = [''];

        foreach ($this->hotkeys as $hotkey) {
            if (mb_strlen(Util::stripEscapeSequences(last($hotkeyLines)) . $hotkey) > $width) {
                $hotkeyLines[] = '';
            }

            $hotkeyLines[count($hotkeyLines) - 1] .= $hotkey . $this->hotkeySpacing();
        }

        foreach ($hotkeyLines as $i => $line) {
            $hotkeyLines[$i] = rtrim($line);
        }

        return $hotkeyLines;
    }
}
