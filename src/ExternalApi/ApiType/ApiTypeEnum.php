<?php
declare(strict_types = 1);

namespace App\ExternalApi\ApiType;

class ApiTypeEnum
{
    public const API_COMMENTS = 'COMMENTS';
    public const API_ISITE = 'ISITE';
    public const API_MORPH = 'MORPH';
    public const API_BRANDING = 'BRANDING';
    public const API_ORBIT = 'ORB';
    public const API_LEGACY = 'LEGACY';


    private const API_TYPES = [
        self::API_COMMENTS => true,
        self::API_ISITE => true,
        self::API_MORPH => true,
        self::API_BRANDING => true,
        self::API_ORBIT => true,
        self::API_LEGACY => true,
    ];

    public static function isValid(string $key)
    {
        return isset(self::API_TYPES[$key]);
    }

    public static function validValues(): array
    {
        return array_keys(self::API_TYPES);
    }
}
