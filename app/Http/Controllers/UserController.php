<?php

namespace App\Http\Controllers;

use App\Helpers\ImageHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UserRequest;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\PositionResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\VacationResource;
use App\Repositories\Implementations\PositionRepository;
use App\Services\LeaveService;
use App\Services\PositionService;
use App\Services\UserService;
use App\Services\VacationService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
        private readonly VacationService $vacationService,
        private readonly LeaveService $leaveService,
        private readonly PositionService $positionService
    ) {}

    public function index(Request $request)
    {
        try{
            return response()->json([
                'users' => UserResource::collection($this->userService->getAllUsers())
            ]);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $response = $this->userService->login($request->email, $request->password);

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if ($this->userService->logout($user)) {
            return response()->json([
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json(['message' => 'Failed to log out'], 500);
    }

    public function show(Request $request, $userId)
    {
        try {
            $user = $request->user();

            if ($user->id !== (int)$userId && $user->role !== 'admin') {
                $userId = $user->id;
            }

            $userData = $this->userService->getUser($userId);

            $additionalData = $this->getUsersData($userData);
            $userData->position = $additionalData['position'];

            return response()->json([
                'user' => new UserResource($userData),
                'vacations' => $additionalData['vacations'] ? new VacationResource($additionalData['vacations']) : null,
                'leaves' => $additionalData['leaves']->map(function (Collection $group) {
                    return LeaveResource::collection($group);
                })
            ]);
        }catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    private function getUsersData($userData)
    {
        try {
            $vacations = $this->vacationService->getUserCurrentVacation($userData->id);
            $leaves = $this->leaveService->getUsersLeaveRequests($userData->id);
            $position = $this->positionService->findPosition($userData->position_id);

            return [
                'vacations' => $vacations,
                'leaves' => $leaves,
                'position' => $position
            ];
        }catch (Exception $e){
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function store(UserRequest $request)
    {
        try{
            $this->authorize('is-admin');

            $attributes = $request->validated();
            $attributes['created_at'] = $attributes['joined_team'];

            $vacationData['number_of_days'] = $attributes['number_of_days'];

            $user = $this->userService->createUser($attributes);
            $vacation = $this->vacationService->createVacationFromUser($user, $vacationData);

            return response()->json([
                'message' => 'User created.',
                'user' => new UserResource($user),
                'vacation' => new VacationResource($vacation)
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

    public function update(UserRequest $request, string $id)
    {
        try{
            $this->authorize('is-admin');

            $attributes = $request->validated();

            if(isset($attributes['joined_team'])){
                $attributes['created_at'] = $attributes['joined_team'];
            }

            $user = $this->userService->updateUser($id, $attributes);

            return response()->json([
                'message' => 'User updated.',
                'user' => new UserResource($user)
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

    public function destroy(Request $request, string $id)
    {
        try{
            $this->authorize('is-admin');

            $this->userService->destroy($id);
            return response()->json(['message' => 'User deleted successfully']);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
    public function uploadProfilePicture(Request $request)
    {
        try {
            $user = $request->user();
            $image = $request->file('image');
            if ($user->profile_picture && file_exists(storage_path('app/public/') . $user->profile_picture)) {
                unlink(storage_path('app/public/') . $user->profile_picture);
            }
            ImageHelper::saveProfilePicture($image, $user);
            return response()->json(['message' => 'Profile picture uploaded.']);
        } catch (\Exception $exception) {
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
