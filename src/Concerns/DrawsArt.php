<?php

namespace Chewie\Concerns;

use Chewie\Art;
use Illuminate\Support\Collection;

trait DrawsArt
{
    protected function artLines(string $path): Collection
    {
        $lines = collect(explode(PHP_EOL, Art::get($path)));

        $longest = $lines->map(fn ($line) => mb_strwidth($line))->max();

        return $lines->map(fn ($line) => mb_str_pad($line, $longest));
    }
}
