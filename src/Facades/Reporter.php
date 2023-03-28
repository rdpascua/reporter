<?php

namespace Laboratory\Reporter\Facades;

use Illuminate\Support\Facades\Facade as BaseFacade;

class Reporter extends BaseFacade
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
