<?php

declare(strict_types=1);

namespace Chewie\Support;

class Frames
{
    protected int $current = 0;

    public function next()
    {
        $this->current++;
    }

    public function current()
    {
        return $this->current;
    }

    public function frame(array $frames)
    {
        return $frames[$this->current % count($frames)];
    }
}
