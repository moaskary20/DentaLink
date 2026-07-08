<?php

namespace App\Support;

class LabServiceCategories
{
    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return [
            'Crown' => __('dentalink.service_categories.crown'),
            'Bridge' => __('dentalink.service_categories.bridge'),
            'Implant' => __('dentalink.service_categories.implant'),
            'Veneer' => __('dentalink.service_categories.veneer'),
            'Denture' => __('dentalink.service_categories.denture'),
            'Orthodontic' => __('dentalink.service_categories.orthodontic'),
        ];
    }
}
