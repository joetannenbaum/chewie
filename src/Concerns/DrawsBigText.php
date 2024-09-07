<?php

namespace Chewie\Concerns;

use Chewie\Output\Lines;
use Illuminate\Support\Collection;

trait DrawsBigText
{
    use DrawsArt;

    protected string $fontDir = '';

    public function bigText(string $message, $spacing = 0): Collection
    {
        $characterWidth = mb_strwidth($this->artLines($this->fontPath('a'))->first());

        $maxLineLength = (int) floor($this->width / $characterWidth) - 1;

        $messageLines = wordwrap(
            string: $message,
            width: $maxLineLength,
            cut_long_words: true,
        );

        $lines = collect(explode(PHP_EOL, $messageLines));

        return $lines->map(fn ($line) => collect(mb_str_split($line)))
            ->map(
                fn ($characters) => collect($characters)
                    ->map(fn ($character) => $this->artLines($this->fontPath($character)))
                    ->map(function ($lines) {
                        $longest = $lines->max(fn ($line) => mb_strwidth($line));

                        return $lines->map(function ($line) use ($longest) {
                            while (mb_strwidth($line) < $longest) {
                                $line .= ' ';
                            }

                            return $line;
                        });
                    }),
            )
            ->map(fn ($letters) => Lines::fromColumns($letters)->spacing($spacing)->lines())
            ->flatMap(fn ($lines, $index) => $index === 0 ? $lines : $lines->prepend(''));
    }

    protected function fontPath(string $character): string
    {
        $character = mb_strtolower($character);

        $character = match ($character) {
            ' '     => 'space',
            '!'     => 'exclamation-mark',
            '?'     => 'question-mark',
            '.'     => 'period',
            ','     => 'comma',
            '('     => 'open-parenthesis',
            ')'     => 'close-parenthesis',
            '['     => 'open-square-bracket',
            ']'     => 'close-square-bracket',
            '/'     => 'forward-slash',
            '<'     => 'open-caret',
            '>'     => 'close-caret',
            '@'     => 'at',
            '#'     => 'hash',
            '$'     => 'dollar',
            '%'     => 'percent',
            '&'     => 'ampersand',
            '*'     => 'asterisk',
            '_'     => 'underscore',
            '-'     => 'hyphen',
            '^'     => 'caret',
            '='     => 'equals',
            '+'     => 'plus',
            "'"     => 'apostrophe',
            '"'     => 'quote',
            default => $character,
        };

        return $this->fontDir . '/' . $character;
    }

    protected function setFontDir(string $fontDir): void
    {
        $this->fontDir = $fontDir;
    }
}
