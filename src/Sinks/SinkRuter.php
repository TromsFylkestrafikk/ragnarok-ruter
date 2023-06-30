<?php

namespace TromsFylkestrafikk\RagnarokRuter\Sinks;

use Illuminate\Support\Carbon;
use TromsFylkestrafikk\RagnarokSink\Sinks\SinkBase;

class SinkRuter extends SinkBase
{
    public $id = "ruter";
    public $title = "Ruter";

    /**
     * @inheritdoc
     */
    public function getFromDate(): Carbon
    {
        return new Carbon('2020-06-01');
    }

    /**
     * @inheritdoc
     */
    public function getToDate(): Carbon
    {
        return today()->subDay();
    }

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
