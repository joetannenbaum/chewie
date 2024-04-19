<?php

namespace Chewie\Concerns;

trait CapturesOutput
{
    protected function captureAndResetOutput()
    {
        $output = $this->output;

        $this->output = '';

        return $output;
    }

    protected function captureOutput(callable $callback)
    {
        $output = $this->captureAndResetOutput();

        $callback();

        $result = $this->output;

        $this->output = $output;

        return $result;
    }

    protected function currentLineCount(): int
    {
        return substr_count($this->output, PHP_EOL);
    }
}
