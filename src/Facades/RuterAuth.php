<?php

namespace Ragnarok\Ruter\Facades;

use Illuminate\Support\Facades\Facade;
use Ragnarok\Ruter\Services\RuterAuthToken;

/**
 * @mixin \Ragnarok\Ruter\Services\RuterAuthToken
 */
class RuterAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RuterAuthToken::class;
    }
}
