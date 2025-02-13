<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamExistsRequest;
use App\Http\Requests\TeamMemberRequest;
use App\Http\Resources\TeamMemberResource;
use App\Repositories\Implementations\TeamMemberRepository;
use App\Services\TeamService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use function PHPUnit\Framework\isEmpty;

class TeamMemberController extends Controller
{
    public function __construct(private readonly TeamService $teamService, private readonly TeamMemberRepository $teamMemberRepo) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamMemberRequest $request)
    {
        try{
            $team = $this->checkAccess($request->team_id);

            $exists = $this->teamMemberRepo->memberExists($request->user_id, $team->id);
            if($exists){
                return response()->json([
                    'message' => 'Team member is already part of the team.',
                ]);
            }

            $teamMember = $this->teamMemberRepo->create($request->validated());

            return response()->json([
                'message' => 'Team member created successfully.',
                'team' => new TeamMemberResource($teamMember)
            ]);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $teamId)
    {
        $members = $this->teamMemberRepo->getMembers($teamId);
        return $members->isNotEmpty() ? TeamMemberResource::collection($members) : response()->json(['message' => 'No members in this team.']);
    }

    /**
     * Display the members who are not in the team
     */
    public function getMembersNotInTeam(TeamExistsRequest $request)
    {
        try{
            $this->authorize('is-admin');

            $members = $this->teamMemberRepo->getMembersNotInTeam($request->validated());
            return TeamMemberResource::collection($members);
        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamMemberRequest $request, string $id)
    {
        try{
            $teamMember = $this->teamMemberRepo->findWith($id, ['user','team']);
            if(!$teamMember){
                throw new Exception('Team member not found.');
            }

            $this->checkAccess($teamMember->team_id);

            $updatedTeamMember = $this->teamMemberRepo->update($teamMember, $request->validated());

            return response()->json([
                'message' => 'Team member updated successfully',
                'data' => new TeamMemberResource($updatedTeamMember)
            ]);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $teamMember = $this->teamMemberRepo->findWith($id, ['user','team']);
            if(!$teamMember){
                throw new Exception('Team member not found.');
            }

            $this->checkAccess($teamMember->team_id);

            $this->teamMemberRepo->destroy($teamMember);
            return response()->json(['message' => 'Team member deleted successfully']);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    private function checkAccess($id)
    {
        $team = $this->teamService->findById($id);
        if (!$team) {
            throw new \Exception('Team not found.', 404);
        }

        $this->authorize('modify-team', $team);
        return $team;
    }
}
