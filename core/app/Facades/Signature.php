<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Signature as BaseClass;

class Signature extends Facade
{
    public static function getFacadeAccessor()
    {
        return BaseClass::class;
    }
}
