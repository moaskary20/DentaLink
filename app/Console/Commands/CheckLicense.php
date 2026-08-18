<?php

namespace App\Console\Commands;

use App\Services\LicenseService;
use Illuminate\Console\Command;

class CheckLicense extends Command
{
    protected $signature = 'license:check';

    protected $description = 'Verify the application license against the remote license server';

    public function handle(LicenseService $licenseService): int
    {
        $valid = $licenseService->refresh();

        if ($valid) {
            $this->info('License is valid.');

            return self::SUCCESS;
        }

        $this->error('License is invalid or could not be verified.');

        return self::FAILURE;
    }
}
