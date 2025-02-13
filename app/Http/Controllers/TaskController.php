<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;
use App\Repositories\Implementations\TaskRepository;
use App\Repositories\Implementations\TeamMemberRepository;
use App\Services\TaskService;
use App\Services\TeamService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskController extends Controller
{
    public function __construct(private readonly TeamService $teamService, private readonly TaskService $taskService, private readonly TeamMemberRepository $teamMemberRepo) {}

    /**
     * Display the specified resource.
     */
    public function index(TaskRequest $request)
    {
        try {
            $user = $request->user();

            if (isset($request->team_id)) {
                if(!$this->checkAccess($request->team_id) && ($request['user_id'] ?? null) !== $user->id){
                    throw new AuthorizationException();
                }
            }
            $validated = $request->validated();

            $userId = $validated['user_id'] ?? null;
            $teamId = $validated['team_id'] ?? null;
            $year = $validated['year'] ?? null;
            $month = $validated['month'] ?? null;

            $tasks = $this->taskService->getTasksByUserAndDate($userId, $teamId, $year, $month);

            return TaskResource::collection($tasks);

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
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        try{
            $this->checkAccess($request->team_id);

            if(!$this->teamMemberRepo->memberExists($request->user_id, $request->team_id)){
                throw new ModelNotFoundException('User is not part of this team.', 400);
            }
            $task = $this->taskService->createTask($request->validated());

            return response()->json([
                'message' => 'Task created successfully.',
                'task' => new TaskResource($task)
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
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, string $id)
    {
        try{
            $task = $this->taskService->findWith($id, ['team', 'user']);

            $this->checkAccess($task->team_id);

            $updatedTask = $this->taskService->updateTask($task, $request->validated());

            return response()->json([
                'message' => 'Task updated successfully.',
                'task' => new TaskResource($updatedTask)
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
    public function destroy(string $id)
    {
        try{
            $task = $this->taskService->findWith($id, ['team', 'user']);
            $this->checkAccess($task->team_id);

            $this->taskService->destroyTask($task);

            return response()->json(['message' => 'Task deleted successfully']);

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

        $this->authorize('modify-task', $team);
        return $team;
    }
}
