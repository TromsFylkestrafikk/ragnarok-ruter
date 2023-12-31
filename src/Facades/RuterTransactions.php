<?php

namespace Ragnarok\Ruter\Facades;

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Carbon;
use Ragnarok\Ruter\Services\RuterTransactions as RuterTransactionsService;

/**
 * @method static string getTransactionsAsJson(Carbon $date)
 * @method static array getTransactionsAsArray(Carbon $date)
 * @method static int import(array $transactions)
 * @method static RuterTransactionsService delete(Carbon $date)
 */
class RuterTransactions extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RuterTransactionsService::class;
    }
}
