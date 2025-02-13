<?php

namespace App\Repositories\Implementations;

use App\Models\TeamMember;
use App\Repositories\Interfaces\TeamMemberRepositoryInterface;

class TeamMemberRepository extends Repository implements TeamMemberRepositoryInterface
{
    public function __construct(TeamMember $model)
    {
        parent::__construct($model);
    }
    public function getMembers($teamId)
    {
        return TeamMember::with(['user','team', 'position'])
            ->where('team_id', $teamId)
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->get();
    }

    public function create($data)
    {
        $teamMember = TeamMember::create($data);
        $teamMember->load(['user', 'team']);
        return $teamMember;
    }

    public function memberExists($userId, $teamId)
    {
        return TeamMember::where('user_id', $userId)
            ->where('team_id', $teamId)
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })
            ->first();
    }

    public function getMembersNotInTeam($teamId)
    {
        return TeamMember::whereNotIn('id', function ($query) use ($teamId) {
            $query->select('id')
                  ->from('team_members')
                  ->where('team_id', $teamId);
            })
            ->whereHas('user', function ($query) {
                $query->whereNull('deleted_at');
            })->get();
    }
}
