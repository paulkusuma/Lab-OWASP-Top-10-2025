<?php

namespace App\Helpers;

class Lab
{
    public static function mode()
    {
        return env('LAB_MODE', false);
    }
}
