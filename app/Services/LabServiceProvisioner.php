<?php

namespace App\Services;

use App\Models\Lab;
use App\Models\LabService;

class LabServiceProvisioner
{
    /**
     * @return list<array{name: string, category: string, price: float|int, turnaround_days: int}>
     */
    public function defaults(): array
    {
        return config('default_lab_services', []);
    }

    public function provision(Lab $lab, bool $onlyMissing = true): int
    {
        $existingNames = $onlyMissing
            ? $lab->services()
                ->pluck('name')
                ->map(fn (string $name) => $this->normalizeName($name))
                ->flip()
            : collect();

        $created = 0;

        foreach ($this->defaults() as $service) {
            $nameKey = $this->normalizeName($service['name']);

            if ($onlyMissing && $existingNames->has($nameKey)) {
                continue;
            }

            LabService::query()->create([
                'lab_id' => $lab->id,
                'name' => $service['name'],
                'category' => $service['category'],
                'price' => $service['price'],
                'turnaround_days' => $service['turnaround_days'],
                'is_active' => true,
            ]);

            $created++;
        }

        return $created;
    }

    public function provisionAll(bool $onlyMissing = true): int
    {
        $total = 0;

        Lab::query()->each(function (Lab $lab) use (&$total, $onlyMissing): void {
            $total += $this->provision($lab, $onlyMissing);
        });

        return $total;
    }

    private function normalizeName(string $name): string
    {
        return strtolower(trim($name));
    }
}
