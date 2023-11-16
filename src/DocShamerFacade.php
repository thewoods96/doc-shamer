<?php

namespace Thewoods96\DocShamer;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Thewoods96\DocShamer\Skeleton\SkeletonClass
 */
class DocShamerFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'doc-shamer';
    }
}
