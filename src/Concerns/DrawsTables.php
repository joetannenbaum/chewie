<?php

namespace Chewie\Concerns;

use Illuminate\Support\Collection;
use Laravel\Prompts\Output\BufferedConsoleOutput;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableStyle;

trait DrawsTables
{
    public function table(array $rows, array $headers = []): Collection
    {
        $buffered = new BufferedConsoleOutput();

        $tableStyle = (new TableStyle())
            ->setHorizontalBorderChars('─')
            ->setVerticalBorderChars('│', '│')
            ->setCellHeaderFormat($this->dim('<fg=default>%s</>'))
            ->setCellRowFormat('<fg=default>%s</>');

        if (empty($headers)) {
            $tableStyle->setCrossingChars('┼', '', '', '', '┤', '┘</>', '┴', '└', '├', '<fg=gray>┌', '┬', '┐');
        } else {
            $tableStyle->setCrossingChars('┼', '<fg=gray>┌', '┬', '┐', '┤', '┘</>', '┴', '└', '├');
        }

        (new Table($buffered))
            ->setHeaders($headers)
            ->setRows($rows)
            ->setStyle($tableStyle)
            ->render();

        return collect(explode(PHP_EOL, trim($buffered->content(), PHP_EOL)));
    }
}
