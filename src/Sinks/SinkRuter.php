<?php

namespace Ragnarok\Ruter\Sinks;

use Exception;
use Illuminate\Support\Carbon;
use Ragnarok\Ruter\Facades\RuterTransactions;
use Ragnarok\Sink\Services\LocalFiles;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Sink\Traits\LogPrintf;

class SinkRuter extends SinkBase
{
    use LogPrintf;

    public static $id = "ruter";
    public static $title = "Ruter";

    /**
     * @var LocalFiles
     */
    protected $ruterFiles = null;

    public function __construct()
    {
        $this->ruterFiles = new LocalFiles(static::$id);
        $this->logPrintfInit('[Sink %s]: ', static::$id);
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
        $file = null;
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
     * @inheritdoc
     */
    public function import($id): bool
    {
        RuterTransactions::import(json_decode(
            gzdecode($this->ruterFiles->getContents($this->chunkFilename($id))),
            true
        ));
        return true;
    }

    /**
     * @inheritdoc
     */
    public function deleteImport($id): bool
    {
        try {
            RuterTransactions::delete(new Carbon($id));
            return true;
        } catch (Exception $except) {
            return false;
        }
    }

    protected function chunkFilename($id)
    {
        return $id . '.json.gz';
    }
}
