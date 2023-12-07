<?php

namespace Ragnarok\Ruter\Sinks;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Ragnarok\Ruter\Facades\RuterTransactions;
use Ragnarok\Sink\Services\LocalFiles;
use Ragnarok\Sink\Models\SinkFile;
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
    public function fetch(string $id): SinkFile|null
    {
        $date = new Carbon($id);
        $content = gzencode(RuterTransactions::getTransactionsAsJson($date));
        return $this->ruterFiles->toFile($this->chunkFilename($id), $content);
    }

    /**
     * @inheritdoc
     */
    public function import(string $id, SinkFile $file): int
    {
        return RuterTransactions::delete($id)->import(json_decode(
            gzdecode($this->ruterFiles->getContents($file)),
            true
        ));
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id): bool
    {
        RuterTransactions::delete(new Carbon($id));
        return true;
    }

    protected function chunkFilename(string $id): string
    {
        return $id . '.json.gz';
    }
}
