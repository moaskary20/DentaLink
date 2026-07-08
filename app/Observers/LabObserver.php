<?php

namespace App\Observers;

use App\Models\Lab;
use App\Services\LabServiceProvisioner;

class LabObserver
{
    public function __construct(private LabServiceProvisioner $provisioner) {}

    public function created(Lab $lab): void
    {
        $this->provisioner->provision($lab);
    }
}
