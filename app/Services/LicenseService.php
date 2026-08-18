<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LicenseService
{
    /**
     * @return array{valid: bool, license: ?string, domain: ?string, last_success_at: int|null, checked_at: int|null}
     */
    public function getState(): array
    {
        return Cache::get(config('license.cache_key'), [
            'valid' => false,
            'license' => null,
            'domain' => null,
            'last_success_at' => null,
            'checked_at' => null,
        ]);
    }

    public function isValid(): bool
    {
        if (! config('license.enabled')) {
            return true;
        }

        $state = $this->getState();

        if ($state['last_success_at'] === null) {
            return $this->refresh();
        }

        if (! ($state['valid'] ?? false)) {
            return false;
        }

        $hoursSinceSuccess = Carbon::createFromTimestamp($state['last_success_at'])
            ->diffInHours(now());

        if ($hoursSinceSuccess > config('license.grace_period_hours')) {
            return $this->refresh();
        }

        return true;
    }

    public function refresh(): bool
    {
        if (! config('license.enabled')) {
            return true;
        }

        $checkedAt = now()->timestamp;

        try {
            $response = Http::timeout(config('license.timeout_seconds'))
                ->acceptJson()
                ->get(config('license.url'));

            if (! $response->successful()) {
                return $this->handleFetchFailure($checkedAt);
            }

            $payload = $response->json();

            if (! is_array($payload)) {
                return $this->handleFetchFailure($checkedAt);
            }

            $valid = $this->validatePayload($payload);

            $this->storeState([
                'valid' => $valid,
                'license' => $payload['license'] ?? null,
                'domain' => $payload['domain'] ?? null,
                'last_success_at' => $checkedAt,
                'checked_at' => $checkedAt,
            ]);

            return $valid;
        } catch (\Throwable $exception) {
            Log::warning('License check failed: '.$exception->getMessage());

            return $this->handleFetchFailure($checkedAt);
        }
    }

    private function handleFetchFailure(int $checkedAt): bool
    {
        $state = $this->getState();

        $this->storeState([
            'valid' => (bool) ($state['valid'] ?? false),
            'license' => $state['license'] ?? null,
            'domain' => $state['domain'] ?? null,
            'last_success_at' => $state['last_success_at'] ?? null,
            'checked_at' => $checkedAt,
        ]);

        if (($state['last_success_at'] ?? null) === null) {
            return false;
        }

        $hoursSinceSuccess = Carbon::createFromTimestamp($state['last_success_at'])
            ->diffInHours(now());

        if ($hoursSinceSuccess > config('license.grace_period_hours')) {
            return false;
        }

        return (bool) ($state['valid'] ?? false);
    }

    /**
     * @param  array{license?: string, domain?: string}  $payload
     */
    private function validatePayload(array $payload): bool
    {
        if (($payload['license'] ?? null) !== 'ACTIVE') {
            return false;
        }

        $licenseDomain = $this->normalizeDomain($payload['domain'] ?? '');
        $currentDomain = $this->normalizeDomain($this->currentDomain());

        return $licenseDomain !== '' && $licenseDomain === $currentDomain;
    }

    private function currentDomain(): string
    {
        if (app()->runningInConsole()) {
            return (string) parse_url(config('app.url'), PHP_URL_HOST);
        }

        return request()->getHost();
    }

    private function normalizeDomain(string $domain): string
    {
        $domain = strtolower(trim($domain));

        if (str_starts_with($domain, 'www.')) {
            $domain = substr($domain, 4);
        }

        return $domain;
    }

    /**
     * @param  array{valid: bool, license: ?string, domain: ?string, last_success_at: ?int, checked_at: ?int}  $state
     */
    private function storeState(array $state): void
    {
        Cache::forever(config('license.cache_key'), $state);
    }
}
