<?php

namespace Ragnarok\Ruter\Sinks;

use Illuminate\Support\Carbon;
use Ragnarok\Ruter\Facades\RuterTransactions;
use Ragnarok\Sink\Services\LocalFile;
use Ragnarok\Sink\Models\SinkFile;
use Ragnarok\Sink\Sinks\SinkBase;

class SinkRuter extends SinkBase
{
    public static $id = "ruter";
    public static $title = "Ruter";

    /**
     * @inheritdoc
     */
    public function destinationTables(): array
    {
        return [
            'ruter_transactions' => 'All transactions made with information about app id, payment method, ticket type, validity, etc.',
            'ruter_passengers' => 'List of passenger associated with a transaction',
        ];
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
    public function import(string $chunkId, SinkFile $file): int
    {
        $local = new LocalFile(self::$id, $file);
        return RuterTransactions::delete(new Carbon($chunkId))->import(
            $chunkId,
            json_decode(gzdecode($local->get()), true)
        );
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
