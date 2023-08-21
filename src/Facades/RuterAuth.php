<?php

namespace TromsFylkestrafikk\RagnarokRuter\Facades;

use Illuminate\Support\Facades\Facade;
use TromsFylkestrafikk\RagnarokRuter\Services\RuterAuthToken;

/**
 * @mixin \TromsFylkestrafikk\RagnarokRuter\Services\RuterAuthToken
 */
class RuterAuth extends Facade
{
    protected static function getFacadeAccessor()
    {
        return RuterAuthToken::class;
    }
}
