<?php

namespace TromsFylkestrafikk\RagnarokRuter\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Carbon;
use TromsFylkestrafikk\RagnarokRuter\Services\RuterTransactions as RuterTransactionsService;

/**
 * @method static string getTransactionsAsJson(Carbon $date)
 * @method static array getTransactionsAsArray(Carbon $date)
 * @method static RuterTransactionsService import(array $transactions)
 * @method static RuterTransactionsService delete(Carbon $date)
 */
class RuterTransactions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RuterTransactionsService::class;
    }
}
