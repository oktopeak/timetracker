<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VacationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'year' => $this->year,
            'number_of_days' => $this->number_of_days,
            'days_used' => $this->number_of_days - $this->days_left,
            'days_left' => $this->days_left,
            'carried_days' => $this->carried_days ?? 0
        ];
    }
}
