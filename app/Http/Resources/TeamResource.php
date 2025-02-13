<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
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
            'name' => $this->name,
            'team_leader' => $this->leader ? new UserResource($this->leader) : 'This team has no team leader set.',
            'created_by' => $this->created_by,
            'is_active' => $this->is_active ? 'Active' : 'Inactive'
        ];
    }
}
