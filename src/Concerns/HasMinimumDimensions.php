<?php

namespace Chewie\Concerns;

trait HasMinimumDimensions
{
    use Aligns;
    use DrawsAscii;

    protected function minDimensions(callable $render, int $width = 0, int $height = 0): string
    {
        $termWidth = $this->prompt->terminal()->cols();
        $termHeight = $this->prompt->terminal()->lines();

        if ($termWidth < $width && $termHeight < $height) {
            return $this->renderTooSmall(
                [
                    $this->bold('Your terminal is too small.'),
                    '',
                    'Please resize your terminal to at least ' . $this->bold($width . ' x ' . $height) . '.',
                    'It is currently ' . $this->bold($termWidth . ' x ' . $termHeight) . '.',
                ],
                $termWidth,
                $termHeight
            );
        }

        if ($termWidth < $width) {
            return $this->renderTooSmall(
                [
                    $this->bold('Your terminal is too narrow.'),
                    '',
                    'Please resize your terminal to at least ' . $this->bold($width) . ' columns.',
                    'It is currently ' . $this->bold($termWidth) . ' columns.',
                ],
                $termWidth,
                $termHeight
            );
        }

        if ($termHeight < $height) {
            return $this->renderTooSmall(
                [
                    $this->bold('Your terminal is too short.'),
                    '',
                    'Please resize your terminal to at least ' . $this->bold($height) . ' rows.',
                    'It is currently ' . $this->bold($termHeight) . ' rows.',
                ],
                $termWidth,
                $termHeight
            );
        }

        return $render();
    }

    protected function renderTooSmall(array $lines, int $termWidth, int $termHeight): string
    {
        $this->center(
            $this->asciiLines('cli-lab')
                ->map(fn ($line) => $this->cyan($line))
                ->push('')
                ->push('')
                ->concat($lines)
                ->push('')
                ->push(
                    $this->dim('Press ') . 'q' . $this->dim(' to quit.')
                ),
            $termWidth - 2,
            $termHeight - 2
        )->each(fn ($line) => $this->line($line));

        return $this;
    }
}
