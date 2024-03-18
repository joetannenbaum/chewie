<?php

declare(strict_types=1);

namespace Chewie;

use Illuminate\Support\Collection;

function stripEscapeSequences(string $text): string
{
    $text = preg_replace("/\e[^m]*m/", '', $text);

    return preg_replace("/<(?:(?:[fb]g|options)=[a-z,;]+)+>(.*?)<\/>/i", '$1', $text);
}

function collectionOf(...$args): Collection
{
    if (count($args) === 1) {
        return collect(range(1, $args[0]));
    }

    return collect(range($args[0], $args[1]));
}
