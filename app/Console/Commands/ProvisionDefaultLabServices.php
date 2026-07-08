<?php

namespace App\Console\Commands;

use App\Services\LabServiceProvisioner;
use Illuminate\Console\Command;

class ProvisionDefaultLabServices extends Command
{
    protected $signature = 'labs:provision-default-services {--force : Re-add all defaults even if a service name already exists}';

    protected $description = 'Add default lab services to all existing labs (skips duplicates by default)';

    public function handle(LabServiceProvisioner $provisioner): int
    {
        $onlyMissing = ! $this->option('force');
        $created = $provisioner->provisionAll($onlyMissing);

        $this->info("Provisioned {$created} service(s) across all labs.");

        return self::SUCCESS;
    }
}
