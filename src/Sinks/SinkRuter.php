<?php

namespace TromsFylkestrafikk\RagnarokRuter\Sinks;

use Exception;
use Illuminate\Support\Carbon;
use TromsFylkestrafikk\RagnarokSink\Sinks\SinkBase;
use TromsFylkestrafikk\RagnarokRuter\Facades\RuterTransactions;
use TromsFylkestrafikk\RagnarokRuter\Services\TransactionFiles;

class SinkRuter extends SinkBase
{
    public $id = "ruter";
    public $title = "Ruter";

    /**
     * @var TransactionFiles
     */
    protected $ruterFiles = null;

    public function __construct()
    {
        $this->ruterFiles = new TransactionFiles($this->id);
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
            $this->ruterFiles->toFile($this->chunkFilename($id), RuterTransactions::getTransactionsAsJson($id));
        } catch (Exception $except) {
            return false;
        }
        return $file ? true : false;
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
        return $id . 'json';
    }
}
