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
}
