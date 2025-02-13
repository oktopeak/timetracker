<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->user->fullName,
            'leave_type' => new LeaveTypeResource($this->leave_type),
            'leave_start' => $this->leave_start->format('Y-m-d'),
            'leave_end' => $this->leave_end->format('Y-m-d'),
            'notes' => $this->notes,
            'status' => $this->status,
            'number_of_days' => $this->number_of_days
        ];
    }
}
