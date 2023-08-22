<?php

namespace TromsFylkestrafikk\RagnarokRuter\Sinks;

use Exception;
use Illuminate\Support\Carbon;
use TromsFylkestrafikk\RagnarokRuter\Facades\RuterTransactions;
use TromsFylkestrafikk\RagnarokSink\Models\RawFile;
use TromsFylkestrafikk\RagnarokSink\Traits\LogPrintf;
use TromsFylkestrafikk\RagnarokSink\Services\LocalFiles;
use TromsFylkestrafikk\RagnarokSink\Sinks\SinkBase;

class SinkRuter extends SinkBase
{
    use LogPrintf;

    public $id = "ruter";
    public $title = "Ruter";

    /**
     * @var LocalFiles
     */
    protected $ruterFiles = null;

    public function __construct()
    {
        $this->ruterFiles = new LocalFiles($this->id);
        $this->logPrintfInit('[Sink %s]: ', $this->id);
    }

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
     * @inheritdoc
     */
    public function fetch($id): bool
    {
        try {
            $date = new Carbon($id);
            $content = gzencode(RuterTransactions::getTransactionsAsJson($date));
            $file = $this->ruterFiles->toFile($this->chunkFilename($id), $content);
        } catch (Exception $except) {
            $this->error("%s[%d]: %s\n, %s", $except->getFile(), $except->getLine(), $except->getMessage(), $except->getTraceAsString());
        }
        return $file ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function removeChunk($id): bool
    {
        $this->ruterFiles->rmFile($this->chunkFilename($id));
        return true;
    }

    /**
     * Import one chunk from sink.
     *
     * @return bool
     */
    public function import(): bool
    {
        Log::debug('Ruter import. Yay!');
        return true;
    }

    protected function chunkFilename($id)
    {
        return $id . '.json.gz';
    }
}
