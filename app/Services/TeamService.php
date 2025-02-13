<?php

namespace App\Services;

use App\Repositories\Implementations\TeamMemberRepository;
use App\Repositories\Implementations\TeamRepository;
use App\Repositories\Implementations\UserRepository;

class TeamService
{
    public function __construct(private readonly TeamRepository $teamRepo,private readonly TeamMemberRepository $teamMemberRepo, private readonly UserRepository $userRepo){}

    public function getTeams()
    {
        $teams = $this->teamRepo->getAll();

        return $teams;
    }

    public function createTeam($userId, $data)
    {
        $data['created_by'] = $userId;
        $team = $this->teamRepo->create($data);

        if(isset($team->team_leader_id)){
            $leaderData = [
                'team_id' => $team->id,
                'user_id' => $team->team_leader_id,
                'role' => 'manager',
            ];
            $this->teamMemberRepo->create($leaderData);
        }
        return $team;
    }

    public function findById($id)
    {
        $team = $this->teamRepo->findById($id);
        if (!$team) {
            throw new \Exception('Team not found.', 404);
        }
        return $team;
    }

    public function update($team, $data)
    {
        if(isset($data['team_leader_id'])) {
            $leader = $this->userRepo->findById($data['team_leader_id']);
            if (!$leader) {
                throw new \Exception("Team Leader not found");
            }
        }
        return $this->teamRepo->update($team, $data);
    }

    public function destroy($team)
    {
        return $this->teamRepo->destroy($team);
    }
}
