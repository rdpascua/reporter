<?php

namespace Rdpascua\Reporter\Facades;

use Illuminate\Support\Facades\Facade;

class Reporter extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'reporter';
    }
}
