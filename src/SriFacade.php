<?php

namespace Naldi\LaravelSri;

use Illuminate\Support\Facades\Facade;

class SriFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'sri';
    }
}
