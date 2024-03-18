<?php

namespace Chewie\Input;

use Laravel\Prompts\Key;
use Laravel\Prompts\Prompt;

class KeyPressListener
{
    protected array $regular = [];

    protected array $escape = [];

    protected $wildcardCallback;

    public function __construct(protected Prompt $prompt)
    {
        //
    }

    public static function for(Prompt $prompt): static
    {
        return new static($prompt);
    }

    public function once(): void
    {
        $fh = fopen('php://stdin', 'r');
        $read = [$fh];
        $write = null;
        $except = null;

        if (stream_select($read, $write, $except, 0) === 1) {
            $key = fread($fh, 10);
            $this->handleKey($key);
        }
    }

    public function clearExisting(): static
    {
        $this->prompt->clearListeners();

        return $this;
    }

    public function listenToInput(&$value, &$cursorPosition)
    {
        return $this
            ->on(
                [Key::LEFT, Key::LEFT_ARROW, Key::CTRL_B],
                function () use (&$cursorPosition) {
                    $cursorPosition = max(0, $cursorPosition - 1);
                }
            )
            ->on(
                [Key::RIGHT, Key::RIGHT_ARROW, Key::CTRL_F],
                function () use (&$value, &$cursorPosition) {
                    $cursorPosition = min(mb_strlen($value), $cursorPosition + 1);
                }
            )
            ->on(
                [Key::HOME, Key::CTRL_A],
                function () use (&$cursorPosition) {
                    $cursorPosition = 0;
                }
            )
            ->on(
                [Key::END, Key::CTRL_E],
                function () use (&$value, &$cursorPosition) {
                    $cursorPosition = mb_strlen($value);
                }
            )
            ->on(
                Key::DELETE,
                function () use (&$value, &$cursorPosition) {
                    $value = mb_substr($value, 0, $cursorPosition) . mb_substr($value, $cursorPosition + 1);
                }
            )
            ->on(
                [Key::BACKSPACE, Key::CTRL_H],
                function () use (&$cursorPosition, &$value) {
                    if ($cursorPosition === 0) {
                        return;
                    }

                    $value = mb_substr($value, 0, $cursorPosition - 1) . mb_substr($value, $cursorPosition);
                    $cursorPosition--;
                },
            )
            ->wildcard(function ($key) use (&$cursorPosition, &$value) {
                $value = mb_substr($value, 0, $cursorPosition) . $key . mb_substr($value, $cursorPosition);
                $cursorPosition++;
            });
    }

    public function on($keys, $callback): static
    {
        $keys = is_array($keys) ? $keys : [$keys];

        foreach ($keys as $key) {
            if (is_array($key)) {
                foreach ($key as $k) {
                    $this->on($k, $callback);
                }

                continue;
            }

            if ($this->isEscape($key)) {
                $this->escape[$key] = $callback;
            } else {
                $this->regular[$key] = $callback;
            }
        }

        return $this;
    }

    public function wildcard(callable $cb): static
    {
        $this->wildcardCallback = $cb;

        return $this;
    }

    public function listenForQuit()
    {
        $this->on(['q', Key::CTRL_C], function () {
            $this->prompt->terminal()->exit();
        });

        return $this;
    }

    public function onUp(callable $callback): static
    {
        return $this->on([Key::UP, Key::UP_ARROW], $callback);
    }

    public function onDown(callable $callback): static
    {
        return $this->on([Key::DOWN, Key::DOWN_ARROW], $callback);
    }

    public function onRight(callable $callback): static
    {
        return $this->on([Key::RIGHT, Key::RIGHT_ARROW], $callback);
    }

    public function onLeft(callable $callback): static
    {
        return $this->on([Key::LEFT, Key::LEFT_ARROW], $callback);
    }

    public function listen()
    {
        $this->prompt->on('key', fn ($key) => $this->handleKey($key));
    }

    protected function handleKey($key)
    {
        if ($this->isEscape($key)) {
            foreach ($this->escape as $escape => $callback) {
                if ($key === $escape) {
                    $callback();

                    return;
                }
            }

            return;
        }

        // Keys may be buffered.
        foreach (mb_str_split($key) as $key) {
            foreach ($this->regular as $regular => $callback) {
                if ($key === $regular) {
                    $callback($key);

                    return;
                }
            }

            if (isset($this->wildcardCallback) && ord($key) >= 32) {
                ($this->wildcardCallback)($key);
            }
        }
    }

    protected function isEscape($key): bool
    {
        return $key[0] === "\e" || in_array($key, [Key::CTRL_B, Key::CTRL_F, Key::CTRL_A, Key::CTRL_E]);
    }
}
