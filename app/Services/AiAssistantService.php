<?php

namespace App\Services;

use App\Enums\ApprovalStatus;
use App\Models\Lab;
use App\Models\LabService;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Collection;

class AiAssistantService
{
    public function matchLabs(User $doctor, ?string $serviceCategory = null, ?string $country = null): Collection
    {
        return Lab::query()
            ->where('approval_status', ApprovalStatus::Approved)
            ->where('is_active', true)
            ->when($country, fn ($q) => $q->where('country', $country))
            ->when($serviceCategory, fn ($q) => $q->whereHas('services', fn ($sq) => $sq->where('category', 'like', "%{$serviceCategory}%")))
            ->withCount(['orders as doctor_orders_count' => fn ($q) => $q->where('doctor_id', $doctor->id)])
            ->get()
            ->map(function (Lab $lab) use ($doctor) {
                $priceScore = max(0, 100 - ($lab->starting_price / 5));
                $ratingScore = ($lab->rating / 5) * 100;
                $speedScore = max(0, 100 - ($lab->avg_turnaround_days * 5));
                $historyScore = min(100, ($lab->doctor_orders_count ?? 0) * 20);
                $match = round(($priceScore * 0.25) + ($ratingScore * 0.35) + ($speedScore * 0.25) + ($historyScore * 0.15));

                $lab->match_score = $match;

                return $lab;
            })
            ->sortByDesc('match_score')
            ->values();
    }

    public function predictDelivery(LabService $service, bool $isExpress = false): \Carbon\Carbon
    {
        $days = max(1, $service->turnaround_days - ($isExpress ? 2 : 0));

        return now()->addDays($days);
    }

    public function validateOrderFiles(array $attachments, array $orderData): array
    {
        $issues = [];
        $warnings = [];

        if (empty($attachments)) {
            $issues[] = 'No files uploaded. At least one dental image or scan is required.';
        }

        $hasImage = collect($attachments)->contains(fn ($path) => preg_match('/\.(jpg|jpeg|png|dicom)$/i', $path));
        $has3d = collect($attachments)->contains(fn ($path) => preg_match('/\.(stl|obj|ply)$/i', $path));

        if (! $hasImage) {
            $warnings[] = 'Missing intraoral photos — accuracy may be reduced.';
        }

        if (! $has3d && in_array($orderData['material'] ?? '', ['Zirconia', 'E-Max'])) {
            $warnings[] = '3D scan recommended for Zirconia/E-Max restorations.';
        }

        if (empty($orderData['tooth_number'])) {
            $issues[] = 'Tooth number or area is required.';
        }

        if (empty($orderData['shade'])) {
            $warnings[] = 'Shade not specified — lab may request confirmation.';
        }

        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'warnings' => $warnings,
            'quality_score' => empty($issues) ? (empty($warnings) ? 95 : 78) : 45,
        ];
    }

    public function chatbotReply(User $doctor, string $prompt): string
    {
        $lower = strtolower($prompt);

        if ($this->promptMatches($prompt, $lower, ['lab', 'recommend', 'مختبر', 'توصية', 'أفضل', 'laboratoire', 'recommand'])) {
            $top = $this->matchLabs($doctor)->first();

            return $top
                ? __('dentalink.ai_chat.lab_recommend', [
                    'name' => $top->name,
                    'rating' => $top->rating,
                    'price' => $top->starting_price,
                    'days' => $top->avg_turnaround_days,
                    'score' => $top->match_score,
                ])
                : __('dentalink.ai_chat.no_lab');
        }

        if ($this->promptMatches($prompt, $lower, ['track', 'order', 'طلب', 'تتبع', 'سجل', 'historique', 'commande'])) {
            $latest = Order::query()->where('doctor_id', $doctor->id)->latest()->first();

            return $latest
                ? __('dentalink.ai_chat.order_status', [
                    'number' => $latest->order_number,
                    'status' => $latest->status->label(),
                    'delivery' => $latest->expected_delivery_at?->format('M j, Y') ?? '—',
                ])
                : __('dentalink.ai_chat.no_orders');
        }

        if ($this->promptMatches($prompt, $lower, ['upload', 'file', 'ملف', 'رفع', 'fichier', 'télévers'])) {
            return __('dentalink.ai_chat.upload_help');
        }

        if ($this->promptMatches($prompt, $lower, ['zirconia', 'crown', 'تاج', 'زركونيا', 'مادة', 'material', 'couronne', 'matériau'])) {
            $service = LabService::query()->where('name', 'like', '%Zirconia%')->orderBy('price')->first();

            return $service
                ? __('dentalink.ai_chat.crown_material', [
                    'lab' => $service->lab?->name,
                    'service' => $service->name,
                    'price' => $service->price,
                    'days' => $service->turnaround_days,
                ])
                : __('dentalink.ai_chat.crown_material_fallback');
        }

        return __('dentalink.ai_chat.default');
    }

    protected function promptMatches(string $prompt, string $lower, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (str_contains($lower, strtolower($keyword)) || str_contains($prompt, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public function detectOverdueOrders(User $doctor): Collection
    {
        return Order::query()
            ->where('doctor_id', $doctor->id)
            ->whereNotIn('status', [\App\Enums\OrderStatus::Completed, \App\Enums\OrderStatus::Cancelled])
            ->where('expected_delivery_at', '<', now())
            ->with('lab')
            ->get();
    }
}
