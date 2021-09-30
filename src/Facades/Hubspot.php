<?php

namespace Jordanbeattie\Hubspot\Facades;
use Illuminate\Support\Facades\Facade;

class Hubspot extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'hubspot';
    }
}
