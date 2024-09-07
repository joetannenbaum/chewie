<?php

namespace Chewie\Concerns;

trait Ticks
{
    protected int $tickCount = 0;

    protected int $pauseFor = 0;

    public function tick(): void
    {
        if ($this->pauseFor > 0) {
            $this->pauseFor--;
        } else {
            $this->onTick();
        }

        $this->tickCount++;
    }

    public function onTick(): void
    {
        //
    }

    protected function isNthTick(int $n): bool
    {
        return $this->tickCount % $n === 0;
    }

    protected function onNthTick(int $n, callable $callback): void
    {
        if ($this->isNthTick($n)) {
            $callback();
        }
    }

    protected function pauseFor(int $n): void
    {
        $this->pauseFor = $n;
    }
}
