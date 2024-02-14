<?php

declare(strict_types=1);

namespace Chewie\Output;

use Illuminate\Support\Collection;

class Util
{
    public static function stripEscapeSequences(string $text): string
    {
        $text = preg_replace("/\e[^m]*m/", '', $text);

        return preg_replace("/<(?:(?:[fb]g|options)=[a-z,;]+)+>(.*?)<\/>/i", '$1', $text);
    }

    public static function range(...$args): Collection
    {
        if (count($args) === 1) {
            return collect(range(1, $args[0]));
        }

        return collect(range($args[0], $args[1]));
    }
}
