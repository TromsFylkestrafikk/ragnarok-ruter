<?php

namespace TromsFylkestrafikk\RagnarokRuter\Sinks;

use TromsFylkestrafikk\RagnarokSink\Sinks\SinkBase;

class SinkRuter extends SinkBase
{
    public $name = "Ruter";

    /**
     * Import one chunk from sink.
     *
     * @return bool
     */
    public function import(): bool
    {
        return true;
    }
}
