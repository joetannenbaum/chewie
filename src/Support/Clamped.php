<?php

namespace Chewie\Support;

class Clamped
{
    protected $minCallback = null;

    protected $maxCallback = null;

    public function __construct(
        protected $minValue = null,
        protected $maxValue = null,
        protected $value = null
    ) {
        $this->value ??= $this->minValue ?? $this->maxValue;
    }

    public function increase($by = 1)
    {
        $this->value = min($this->maxValue ?? INF, $this->value + $by);

        if ($this->maxCallback && $this->value === $this->maxValue) {
            ($this->maxCallback)();
        }
    }

    public function decrease($by = 1)
    {
        $this->value = max($this->minValue ?? -INF, $this->value - $by);

        if ($this->minCallback && $this->value === $this->minValue) {
            ($this->minCallback)();
        }
    }

    public function value()
    {
        return $this->value;
    }

    public function onMin($cb)
    {
        $this->minCallback = $cb;
    }

    public function onMax($cb)
    {
        $this->maxCallback = $cb;
    }
}
