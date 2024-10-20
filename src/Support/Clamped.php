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
        $this->value ??= $this->minValue;
    }

    public function increase($by = 1)
    {
        $this->value = min($this->maxValue, $this->value + $by);

        if ($this->value === $this->maxValue) {
            ($this->maxCallback)();
        }
    }

    public function decrease($by = 1)
    {
        $this->value = max($this->minValue, $this->value - $by);

        if ($this->value === $this->minValue) {
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
