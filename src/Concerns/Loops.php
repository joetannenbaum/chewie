<?php

namespace Chewie\Concerns;

use Carbon\CarbonInterval;
use Chewie\Contracts\Loopable;

trait Loops
{
    public array $loopables = [];

    protected int $sleepBetweenLoops = 50_000;

    public function sleepFor(int $microseconds): static
    {
        $this->sleepBetweenLoops = $microseconds;

        return $this;
    }

    protected function registerLoopable(Loopable $component): void
    {
        $this->loopables[] = $component;
    }

    protected function removeLoopable(Loopable $component): void
    {
        $this->loopables = array_filter($this->loopables, fn($loopable) => $loopable !== $component);
    }

    protected function registerLoopables(Loopable ...$components): void
    {
        $this->loopables = array_merge($this->loopables, $components);
    }

    protected function clearRegisteredLoopables(): void
    {
        $this->loopables = [];
    }

    protected function loop($cb, int|CarbonInterval $sleepFor = 50_000)
    {
        if ($sleepFor instanceof CarbonInterval) {
            $sleepFor = $sleepFor->totalMicroseconds;
        }

        $this->sleepBetweenLoops = $sleepFor;

        while (true) {
            $continue = $cb($this);

            if ($continue === false) {
                break;
            }

            foreach ($this->loopables as $component) {
                $component->tick();
            }

            usleep($this->sleepBetweenLoops);
        }
    }
}
