<?php

namespace Chewie\Concerns;

use Chewie\Output\Util;
use Illuminate\Support\Collection;

trait Aligns
{
    protected function centerHorizontally(string|iterable $lines, int $width, string $spacingChar = ' '): Collection
    {
        $lines = $this->toCollection($lines);

        $lineLengths = $lines->map(fn ($line) => mb_strwidth(Util::stripEscapeSequences($line)));

        $maxLineLength = $lineLengths->max();

        $basePadding = floor(($width - $maxLineLength) / 2);

        $result = $lines->map(function ($line) use ($basePadding, $maxLineLength, $spacingChar) {
            $lineLength = mb_strwidth(Util::stripEscapeSequences($line));
            $padding = max($basePadding + floor((($maxLineLength - $lineLength) / 2)), 0);

            return str_repeat($spacingChar, $padding) . $line . str_repeat($spacingChar, $padding);
        });

        $maxLine = $result->max(fn ($line) => mb_strwidth(Util::stripEscapeSequences($line)));

        return $result->map(function ($line) use ($maxLine, $spacingChar) {
            $lineLength = mb_strwidth(Util::stripEscapeSequences($line));

            return $line . str_repeat($spacingChar, $maxLine - $lineLength);
        });
    }

    protected function spaceBetween(int $width, string ...$items)
    {
        $totalLength = collect($items)->map(fn ($item) => mb_strwidth(Util::stripEscapeSequences($item)))->sum();
        $space = $width - $totalLength;
        $spacePerItem = floor($space / (count($items) - 1));

        $result = '';

        foreach ($items as $i => $item) {
            $result .= $item;

            if ($i < count($items) - 1) {
                $result .= str_repeat(' ', $spacePerItem);
            }
        }

        return $result;
    }

    protected function centerVertically(string|iterable $lines, int $height): Collection
    {
        $lines = $this->toCollection($lines);
        $paddingTop = floor(($height / 2)) - floor($lines->count() / 2);

        foreach (Util::range($paddingTop) as $i) {
            $lines->prepend('');
            $lines->push('');
        }

        // In case we overflowed a bit, trim the bottom
        while ($lines->count() > $height) {
            $lines->pop();
        }

        return $lines;
    }

    protected function center(string|iterable $lines, int $width, int $height, string $spacingChar = ' '): Collection
    {
        return $this->centerVertically($this->centerHorizontally($lines, $width, $spacingChar), $height);
    }

    protected function toCollection(string|iterable $lines): Collection
    {
        $lines = is_string($lines) ? explode(PHP_EOL, $lines) : $lines;

        return collect($lines);
    }

    protected function pinToBottom(int $height, $cb)
    {
        // Count line breaks in current string
        $lineBreaks = substr_count($this->output, PHP_EOL);

        $originalOutput = $this->output;
        $this->output = '';

        $cb();

        $newOutput = $this->output;
        $this->output = $originalOutput;

        $padding = $height - $lineBreaks - substr_count($newOutput, PHP_EOL);

        if ($padding > 0) {
            $this->newLine($padding);
        }

        $this->output .= $newOutput;
    }

    protected function padVertically(Collection $lines, int $to)
    {
        while ($lines->count() < $to) {
            $lines->push('');
        }

        return $lines;
    }
}
