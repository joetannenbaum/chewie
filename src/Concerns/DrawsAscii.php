<?php

namespace Chewie\Concerns;

use Illuminate\Support\Collection;

trait DrawsAscii
{
    protected function asciiLines(string $path): Collection
    {
        $lines = collect(explode(PHP_EOL, file_get_contents(storage_path('ascii/' . $path . '.txt'))));

        $longest = $lines->map(fn ($line) => mb_strwidth($line))->max();

        return $lines->map(fn ($line) => mb_str_pad($line, $longest));
    }
}
