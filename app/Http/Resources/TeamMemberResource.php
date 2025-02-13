<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamMemberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'team_member_id' => $this->id,
            'user_id' => $this->user->id,
            'full_name' => $this->user->fullName,
            'position' => $this->position ? $this->position->name : ''
        ];
    }
}
