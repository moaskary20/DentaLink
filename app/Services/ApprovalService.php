<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Enums\UserRole;
use App\Models\ApprovalRequest;
use App\Models\DoctorProfile;
use App\Models\Lab;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ApprovalService
{
    public function approve(ApprovalRequest $request, int $reviewerId): void
    {
        $request->update([
            'status' => ApprovalStatus::Approved,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        $approvable = $request->approvable;

        if ($approvable instanceof Lab) {
            $approvable->update(['approval_status' => ApprovalStatus::Approved, 'is_active' => true]);
            $approvable->user?->update(['is_verified' => true]);
        }

        if ($approvable instanceof DoctorProfile) {
            $approvable->user?->update(['is_verified' => true]);
        }
    }

    public function reject(ApprovalRequest $request, int $reviewerId): void
    {
        $request->update([
            'status' => ApprovalStatus::Rejected,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);

        $approvable = $request->approvable;

        if ($approvable instanceof Lab) {
            $approvable->update(['approval_status' => ApprovalStatus::Rejected, 'is_active' => false]);
        }
    }

    public function registerDoctor(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Doctor,
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'locale' => $data['locale'] ?? 'en',
            'is_verified' => false,
        ]);

        $profile = DoctorProfile::query()->create([
            'user_id' => $user->id,
            'clinic_name' => $data['clinic_name'] ?? null,
            'specialization' => $data['specialization'] ?? null,
            'license_number' => $data['license_number'] ?? null,
            'license_file' => $data['license_file'] ?? null,
            'bio' => $data['bio'] ?? null,
        ]);

        ApprovalRequest::query()->create([
            'approvable_type' => DoctorProfile::class,
            'approvable_id' => $profile->id,
            'requested_by' => $user->id,
            'status' => ApprovalStatus::Pending,
            'notes' => 'Doctor registration — pending admin verification.',
        ]);

        \App\Models\Wallet::query()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 500, 'currency' => 'USD']
        );

        return $user;
    }

    public function registerLab(array $data): User
    {
        $user = User::query()->create([
            'name' => $data['contact_name'] ?? $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => UserRole::Lab,
            'phone' => $data['phone'] ?? null,
            'country' => $data['country'] ?? null,
            'locale' => $data['locale'] ?? 'en',
            'is_verified' => false,
        ]);

        $lab = Lab::query()->create([
            'user_id' => $user->id,
            'name' => $data['lab_name'],
            'country' => $data['country'],
            'city' => $data['city'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'],
            'license_file' => $data['license_file'] ?? null,
            'specialties' => $data['specialties'] ?? [],
            'approval_status' => ApprovalStatus::Pending,
            'is_active' => false,
            'starting_price' => $data['starting_price'] ?? 200,
            'avg_turnaround_days' => $data['avg_turnaround_days'] ?? 7,
        ]);

        ApprovalRequest::query()->create([
            'approvable_type' => Lab::class,
            'approvable_id' => $lab->id,
            'requested_by' => $user->id,
            'status' => ApprovalStatus::Pending,
            'notes' => "Lab registration: {$lab->name} — pending admin approval.",
        ]);

        return $user;
    }
}
