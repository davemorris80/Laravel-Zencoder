<?php namespace A4M\Zencoder;

use Illuminate\Support\Facades\Facade as IlluminateFacade;

class ZencoderFacade extends IlluminateFacade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'zencoder'; }

}