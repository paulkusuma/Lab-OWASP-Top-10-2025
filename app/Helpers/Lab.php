<?php

namespace App\Helpers;

class Lab
{
    public static function mode()
    {
        return env('LAB_MODE', false);
    }

    public static function supplyChainInfo()
    {
        if (!self::mode()) {
            return null;
        }

        return [
            'framework' => 'laravel/framework',
            'known_issue' => 'CVE-2025-27515',
            'risk' => 'File Validation Bypass',
            'abandoned_packages' => [
                'fruitcake/laravel-cors',
                'swiftmailer/swiftmailer',
            ],
        ];
    }
}
