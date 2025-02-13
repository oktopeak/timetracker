<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckInDateRangeRequest;
use App\Http\Requests\CheckInDateRequest;
use App\Http\Requests\CheckInRequest;
use App\Http\Resources\CheckInResource;
use App\Http\Resources\CheckInsListResource;
use App\Http\Resources\LastCheckinResource;
use App\Http\Resources\UserCheckInResource;
use App\Services\CheckInService as ServicesCheckInService;
use Exception;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    public function __construct(private readonly ServicesCheckInService $checkInService) {}

    public function checkInPin(CheckInRequest $request)
    {
        try {
            $attributes = $request->validated();

            $checkInMessage = $this->checkInService->storeCheckInPin($attributes['pin'], $attributes['action']);

            return response()->json(['message' => $checkInMessage ? 'Check-in successful' : 'Check-in failed']);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function checkIn(Request $request)
    {
        try {
            $user = $request->user();

            $attributes = $request->validate([
                'action' => 'required|in:check-in,check-out'
            ]);

            $checkInMessage = $this->checkInService->createCheckIn($user, $attributes['action']);

            return response()->json(['message' => $checkInMessage ? 'Check-in successful' : 'Check-in failed']);

        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getUsersCheckInsToday()
    {
        $checkIns = $this->checkInService->getUsersCheckInsToday();

        return CheckInsListResource::collection($checkIns);
    }

//    public function getUsersCheckIns(CheckInRequest $request)
//    {
//        $checkIns = $this->checkInService->getUsersCheckIns($request->validated());
//        return CheckInsListResource::collection($checkIns);
//    }
    public function getUsersWithLastCheckIns()
    {
        $checkIns = $this->checkInService->getUsersWithLastCheckIns()->sortBy('full_name');
        return LastCheckinResource::collection($checkIns);
    }

    public function getUsersCheckInsByYearMonth(CheckInDateRequest $request, $userId)
    {
        try {
            $user = $request->user();

            if ($user->id !== (int)$userId && $user->role !== 'admin') {
                $userId = $user->id;
            }

            $year = $request->query('year', date('Y'));
            $month = $request->query('month', date('m'));

            $userCheckIns = $this->checkInService->getCheckInsForUser($userId, $year, $month);

            return new CheckInResource($userCheckIns);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function getCheckInsByDateRange(CheckInDateRangeRequest $checkInDateRequest, $userId)
    {
        try {
            $user = $checkInDateRequest->user();

            if ($user->id !== (int)$userId && $user->role !== 'admin') {
                $userId = $user->id;
            }

            $startDate = $checkInDateRequest->query('start_date');
            $endDate = $checkInDateRequest->query('end_date');

            $response = $this->checkInService->getCheckInsByDateRange($userId, $startDate, $endDate);

            return new CheckInResource($response);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
