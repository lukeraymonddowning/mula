<?php

namespace Lukeraymonddowning\Mula;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Lukeraymonddowning\Mula\Skeleton\SkeletonClass
 */
class MulaFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'mula';
    }
}
