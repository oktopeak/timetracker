<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CheckInsListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = $this->first()->user;
        $checkInsFormatted = $this->formatListCheckIns($this);

        return [
            'full_name' => "{$user->name} {$user->surname}",
            'check_in' => $checkInsFormatted['check-in'] ?? null,
            'check_out' => $checkInsFormatted['check-out'] ?? null,
        ];
    }

    private function formatListCheckIns($checkIns)
    {
        $checkIns = $checkIns->take(2);

        $firstCheckIn = $checkIns->first();

        if ($firstCheckIn && $firstCheckIn->action !== 'check-in' && $firstCheckIn->action !== 'check-out') {
            return [
                'check-in' => $firstCheckIn->action,
            ];
        }

        $formatted = $checkIns->map(function ($checkIn) {
            return $checkIn->date->format('H:i') . ' ' . $checkIn->action;
        });

        $actions = [
            'check-in' => null,
            'check-out' => null
        ];


        if ($firstCheckIn->action === 'check-in') {
            $actions['check-in'] = $formatted->first();
            $actions['check-out'] = ' ';
        } else if ($firstCheckIn->action === 'check-out') {
            $secondCheckIn = $checkIns->skip(1)->first();

            if ($secondCheckIn && $secondCheckIn->action === 'check-in') {
                $actions['check-in'] = $formatted->skip(1)->first();
                $actions['check-out'] = $formatted->first();
            } else {
                $actions['check-in'] = ' ';
                $actions['check-out'] = $formatted->first();
            }
        }
        return $actions;
    }
}
