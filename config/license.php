<?php

return [
    'enabled' => env('LICENSE_CHECK_ENABLED', true),

    'url' => env('LICENSE_URL', 'https://caesar-agency.co.uk/license.json'),

    'cache_key' => 'license.state',

    'grace_period_hours' => (int) env('LICENSE_GRACE_PERIOD_HOURS', 24),

    'timeout_seconds' => (int) env('LICENSE_TIMEOUT_SECONDS', 10),
];
