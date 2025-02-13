<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'position' => new PositionResource($this->position),
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'birthday' => $this->birthday,
            'number_of_days' => $this->vacation->number_of_days ?? null,
            'joined_team' => $this->joined_team,
            'profile_picture' => asset('storage/' . $this->profile_picture)
            //'is_active' => $this->is_active ? 'Active' : 'Inactive',
        ];

        if ($request->user()->role === 'admin') {
            $data['is_deleted'] = $this->trashed();
        }

        return $data;
    }
}
