<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Http\Resources\TeamResource;
use App\Services\TeamService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function __construct(private readonly TeamService $teamService) {}

    /**
     * Get a list of all teams
     */
    public function index()
    {
        $teams = $this->teamService->getTeams();
        return TeamResource::collection($teams);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        try{
            $user = $request->user();

            $this->authorize('is-admin');

            $team = $this->teamService->createTeam($user->id, $request->validated());

            return response()->json([
                'message' => 'Team created successfully.',
                'team' => new TeamResource($team)
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
    public function show(string $id)
    {
        try {
            $team = $this->teamService->findById($id);
            return new TeamResource($team);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, string $id)
    {
        try{
            $team = $this->checkAccess($id);

            $updatedTeam = $this->teamService->update($team, $request->validated());

            return response()->json([
                'message' => 'Team updated successfully',
                'data' => new TeamResource($updatedTeam->refresh())
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        try{
            $team = $this->checkAccess($id);

            $this->teamService->destroy($team);
            return response()->json(['message' => 'Team deleted successfully']);

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
