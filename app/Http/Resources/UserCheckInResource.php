<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCheckInResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user->id,
            'full_name' => $this->user->name . ' ' . $this->user->surname,
            'last_check_in' => $this->action,
            'last_check_in_time' => $this->date->format('Y-m-d H:i')
        ];
    }
}
