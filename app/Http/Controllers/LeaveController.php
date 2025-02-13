<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInDateRequest;
use App\Http\Requests\LeaveDateRequest;
use App\Http\Requests\LeaveRequest;
use App\Http\Resources\LeaveOverviewResource;
use App\Http\Resources\LeaveResource;
use App\Http\Resources\VacationResource;
use App\Repositories\Implementations\LeaveRepository;
use App\Services\LeaveService;
use App\Services\VacationService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class LeaveController extends Controller
{
    public function __construct(private readonly LeaveService $leaveService, private readonly VacationService $vacationService, private readonly LeaveRepository $leaveRepository) {
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeaveRequest $request)
    {
        try {
            $user = $request->user();

            $attributes = $request->validated();

            $leave = $this->leaveService->createLeaveRequest($user, $attributes);

            return response()->json([
                'message' => 'Leave request created successfully',
                'leave' => new LeaveResource($leave)
            ], 200);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Get all leave requests with the status pending
     */
    public function getPending()
    {
        try {
            $this->authorize('is-admin');

            $pendingRequests = $this->leaveService->getPendingLeaveRequests();

            $data = $pendingRequests->map(function ($leave) {
                $vacation = $leave->user_id ? $this->vacationService->getUserCurrentVacation($leave->user_id) : null;

                $leaveRequestData = (new LeaveResource($leave))->toArray(request());
                $vacationData = $vacation ? (new VacationResource($vacation))->toArray(request()) : [];

                return array_merge($leaveRequestData, $vacationData);
            })->filter()->values();

            return response()->json(['leave_requests' => $data]);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getLeavesOverview(LeaveDateRequest $request)
    {
        try {
            $this->authorize('is-admin');

            $year = $request->query('year', null);
            $month = $request->query('month', null);

            $groupedLeaves = $this->leaveService->getApprovedRequests($year, $month);

            $formattedLeaves = $groupedLeaves->map(function ($leaves) {
                $leaveData = $leaves->map(function ($leave) {
                    return new LeaveOverviewResource($leave);
                });
                return [
                    'user' => $leaves->first()->user->fullName,
                    'leaves' => $leaveData
                ];
            });
            return response()->json($formattedLeaves);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function show(Request $request, string $id)
    {
        try {
            $user = $request->user();

            if ($user->id !== (int)$id && $user->role !== 'admin') {
                $id = $user->id;
            }
            $leaves = $this->leaveService->getUsersLeaveRequests($id);
            return $leaves->map(function (Collection $group) {
                return LeaveResource::collection($group);
            });

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeaveRequest $request, string $id)
    {
        try {
            $user = $request->user();

            $this->authorize('is-admin');

            if ($request->status === 'approved') {
                $this->leaveService->processApproval($id);
            }

            $leave = $this->leaveService->updateStatus($user, $id, $request->status);
            return response()->json([
                'message' => 'Leave request status updated successfully',
                'leave' => new LeaveResource($leave),
            ], 200);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function getLeavesForUser(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $pagination = $request->get('pagination', 10);
            $leaves = $this->leaveRepository->getLeavesForUser($user, $pagination);
            return LeaveResource::collection($leaves)->toResponse(request());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
