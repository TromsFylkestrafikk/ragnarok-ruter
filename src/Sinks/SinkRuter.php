<?php

namespace Ragnarok\Ruter\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Ruter\Facades\RuterTransactions;
use Ragnarok\Sink\Services\LocalFile;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;
use Ragnarok\Sink\Traits\LogPrintf;

class SinkRuter extends SinkBase
{
    use LogPrintf;

    public static $id = "ruter";
    public static $title = "Ruter";

    public function __construct()
    {
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
        $content = gzencode(RuterTransactions::getTransactionsAsJson(new Carbon($id)));
        return LocalFile::createFromFilename(self::$id, $this->chunkFilename($id))
            ->put($content)
            ->getFile();
    }

    /**
     * @inheritdoc
     */
    public function import(string $id, SinkFile $file): int
    {
        $local = new LocalFile(self::$id, $file);
        return RuterTransactions::delete(new Carbon($id))->import(json_decode(
            gzdecode($local->get()),
            true
        ));
    }

    /**
     * @inheritdoc
     */
    public function deleteImport(string $id, SinkFile $file): bool
    {
        RuterTransactions::delete(new Carbon($id));
        return true;
    }

    protected function chunkFilename(string $id): string
    {
        return $id . '.json.gz';
    }
}
