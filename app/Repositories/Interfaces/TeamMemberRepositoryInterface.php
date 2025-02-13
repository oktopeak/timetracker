<?php

namespace App\Repositories\Interfaces;

interface TeamMemberRepositoryInterface
{
    public function getMembers($id);
    public function create($data);
    public function memberExists($userId, $teamid);
    public function getMembersNotInTeam($id);
}
