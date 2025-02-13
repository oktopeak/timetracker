<?php

namespace App\Http\Controllers;


use App\Http\Requests\CreateLeaveTypeRequest;
use App\Http\Resources\LeaveTypeResource;
use App\Repositories\Implementations\LeaveTypeRepository;
use App\Services\LeaveTypeService;
use Illuminate\Auth\Access\AuthorizationException;

class LeaveTypeController extends Controller
{
    public function __construct(private readonly LeaveTypeService $leaveTypeService){}

    public function index()
    {
        return LeaveTypeResource::collection($this->leaveTypeService->getAll());
    }

    public function store(CreateLeaveTypeRequest $request)
    {
        try {
            $this->authorize('is-admin');

            $leaveType = $this->leaveTypeService->createLeaveType($request->validated());

            return new LeaveTypeResource($leaveType);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function update(CreateLeaveTypeRequest $request, int $id)
    {
        try {
            $this->authorize('is-admin');

            $leaveType = $this->leaveTypeService->updateLeaveType($id, $request->validated());

            return new LeaveTypeResource($leaveType);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->authorize('is-admin');

            $this->leaveTypeService->destroy($id);

            return response()->json(['message' => 'Leave type deleted successfully']);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
