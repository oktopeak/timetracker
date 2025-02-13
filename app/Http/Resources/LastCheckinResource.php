<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LastCheckinResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $checkInsFormatted = $this->formatListCheckIns($this->lastCheckins);

        return [
            'full_name' => $this->full_name,
            'user_id' => $this->id,
            'check_in' => $checkInsFormatted['check-in'] ?? null,
            'check_out' => $checkInsFormatted['check-out'] ?? null,
        ];
    }

    private function formatListCheckIns($checkIns)
    {
        $checkIns = $checkIns->take(2);
        if ($checkIns->count() == 0){
            return [
                'check-in' => null,
                'check-out' => null,
            ];
        }
        $firstCheckIn = $checkIns->first();

        if ($firstCheckIn && $firstCheckIn->action !== 'check-in' && $firstCheckIn->action !== 'check-out') {
            return [
                'check-in' => $firstCheckIn->action,
            ];
        }

        $formatted = $checkIns->map(function ($checkIn) {
            return $checkIn->date->format('H:i');
        });

        $actions = [
            'check-in' => null,
            'check-out' => null
        ];


        if ($firstCheckIn->action === 'check-in') {
            $actions['check-in'] = $formatted->first();
        } else if ($firstCheckIn->action === 'check-out') {
            $secondCheckIn = $checkIns->skip(1)->first();

            if ($secondCheckIn && $secondCheckIn->action === 'check-in') {
                $actions['check-in'] = $formatted->skip(1)->first();
                $actions['check-out'] = $formatted->first();
            } else {
                $actions['check-out'] = $formatted->first();
            }
        }
        return $actions;
    }
}
