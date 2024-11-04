<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class InstitutionFacade extends Facade
{
    public static function getFacadeAccessor()
    {
        return 'institution-attestations-details';
    }
}
