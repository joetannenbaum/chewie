<?php

declare(strict_types=1);

namespace Chewie\Output;

use Illuminate\Support\Collection;

class Lines
{
    protected int $spacing = 0;

    protected string $spacingCharacter = ' ';

    protected string $paddingCharacter = ' ';

    protected $align = 'top';

    public function __construct(protected Collection $lines)
    {
    }

    public static function fromColumns(iterable $input): static
    {
        return new static(collect($input));
    }

    public function align(string $align): static
    {
        $this->align = $align;

        return $this;
    }

    public function alignBottom(): static
    {
        return $this->align('bottom');
    }

    public function alignTop(): static
    {
        return $this->align('top');
    }

    public function alignNone(): static
    {
        return $this->align('none');
    }

    public function spacing(int $spacing): static
    {
        $this->spacing = $spacing;

        return $this;
    }

    public function spacingCharacter(string $spacingCharacter): static
    {
        $this->spacingCharacter = $spacingCharacter;

        return $this;
    }

    public function paddingCharacter(string $paddingCharacter): static
    {
        $this->paddingCharacter = $paddingCharacter;

        return $this;
    }

    public function lines(): Collection
    {
        if ($this->lines->count() === 1) {
            return $this->lines->first();
        }

        $between = str_repeat($this->spacingCharacter, $this->spacing);

        $max = $this->lines->map(fn ($col) => collect($col)->count())->max();

        $lines = $this->lines->map(fn ($col) => collect($col))->map(function (Collection $col) use ($max) {
            $maxLength = $col->max(fn ($l) => mb_strlen(Util::stripEscapeSequences($l)));
            $spaces = str_repeat($this->paddingCharacter, $maxLength ?? 0);

            if (in_array($this->align, ['top', 'bottom'])) {
                // Make sure all lines have the same length
                $col = $col->map(function ($l) use ($maxLength) {
                    $length = mb_strlen(Util::stripEscapeSequences($l));

                    if ($length < $maxLength) {
                        $l .= str_repeat($this->paddingCharacter, $maxLength - $length);
                    }

                    return $l;
                });
            }

            if ($this->align === 'bottom') {
                while ($col->count() < $max) {
                    $col->prepend($spaces);
                }
            }

            if ($this->align === 'top') {
                while ($col->count() < $max) {
                    $col->push($spaces);
                }
            }

            return $col;
        });

        $result = collect($lines->shift());

        if ($lines->isEmpty()) {
            return $result;
        }

        return $result->zip(...$lines)->map(fn ($line) => $line->implode($between));
    }
}
