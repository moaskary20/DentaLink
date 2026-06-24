<?php

namespace App\Support;

class DentaLinkLocale
{
    public const SUPPORTED = ['en', 'ar', 'fr'];

    public static function isSupported(?string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }

    public static function labels(): array
    {
        return [
            'en' => __('dentalink.languages.en'),
            'ar' => __('dentalink.languages.ar'),
            'fr' => __('dentalink.languages.fr'),
        ];
    }

    public static function isRtl(?string $locale = null): bool
    {
        return ($locale ?? app()->getLocale()) === 'ar';
    }

    public static function direction(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }
}
