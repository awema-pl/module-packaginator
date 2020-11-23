<?php

namespace AwemaPL\Packaginator\Facades;

use AwemaPL\Packaginator\Contracts\Packaginator as PackaginatorContract;
use Illuminate\Support\Facades\Facade;

class Packaginator extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return PackaginatorContract::class;
    }
}
