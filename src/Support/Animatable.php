<?php

declare(strict_types=1);

namespace Chewie\Support;

use Exception;

class Animatable
{
    protected int|float $nextValue;

    protected int|float $pauseAfter = 0;

    protected int $pauseFor = 0;

    protected int $delay = 0;

    protected bool $firstLoop = true;

    public function __construct(
        protected int|float $value,
        protected int|float|null $lowerLimit = null,
        protected int|float|null $upperLimit = null,
        protected int|float $step = 1,
    ) {
        $this->nextValue = $value;
    }

    public static function fromRandom(int|float $lowerLimit, int|float $upperLimit): static
    {
        return new static(rand($lowerLimit, $upperLimit), $lowerLimit, $upperLimit);
    }

    public static function fromValue(int|float $value): static
    {
        return new static($value);
    }

    public function whenDoneAnimating(callable $callback): void
    {
        if ($this->isAnimating()) {
            $this->animate();
        } else {
            $callback();
        }
    }

    public function lowerLimit(int|float $lowerLimit): static
    {
        $this->lowerLimit = $lowerLimit;

        return $this;
    }

    public function upperLimit(int|float $upperLimit): static
    {
        $this->upperLimit = $upperLimit;

        return $this;
    }

    public function toggle(int|float $from = null, int|float $to = null): static
    {
        $from = $from ?? $this->lowerLimit;
        $to = $to ?? $this->upperLimit;

        $this->to($this->value === $from ? $to : $from);

        return $this;
    }

    public function step(int|float $step): static
    {
        $this->step = $step;

        return $this;
    }

    public function delay(int $delay): static
    {
        $this->delay = $delay;

        return $this;
    }

    public function pauseAfter(int $pauseAfter): static
    {
        $this->pauseAfter = $pauseAfter;

        return $this;
    }

    public function animate(): void
    {
        if ($this->delay) {
            if ($this->delay > 0) {
                $this->delay--;
            }

            return;
        }

        if ($this->value === $this->nextValue) {
            $this->pauseFor = max(0, $this->pauseFor - 1);

            return;
        }

        $diff = abs($this->value - $this->nextValue);

        if ($diff < $this->step) {
            $this->value = $this->nextValue;
        } else {
            $this->value += $this->value < $this->nextValue ? $this->step : -$this->step;
        }
    }

    public function isAnimating(): bool
    {
        return $this->value !== $this->nextValue || $this->pauseFor > 0;
    }

    public function isAtLowerLimit(): bool
    {
        return $this->value === $this->lowerLimit;
    }

    public function isAtUpperLimit(): bool
    {
        return $this->value === $this->upperLimit;
    }

    public function to(int|float $value): void
    {
        if ($this->value === $value) {
            return;
        }

        if ($this->upperLimit !== null) {
            $value = min($this->upperLimit, $value);
        }

        if ($this->lowerLimit !== null) {
            $value = max($this->lowerLimit, $value);
        }

        $this->nextValue = $value;
        $this->pauseFor = $this->pauseAfter;
    }

    public function toRelative(int|float $value): void
    {
        $this->to($this->value + $value);
    }

    public function toRandom(): void
    {
        if ($this->lowerLimit === null || $this->upperLimit === null) {
            throw new Exception('Lower and upper limits must be set to use random');
        }

        do {
            $value = rand($this->lowerLimit, $this->upperLimit);
        } while ($value === $this->nextValue);

        $this->to($value);
    }

    public function current(): int|float
    {
        return $this->value;
    }

    public function update(int|float $value): void
    {
        $this->value = $value;
    }

    public function next(): int|float
    {
        return $this->nextValue;
    }
}
