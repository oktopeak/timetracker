<?php

namespace App\Http\Controllers;

use App\Http\Requests\PersonalHolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Services\PersonalHolidayService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PersonalHolidayController extends Controller
{
    public function __construct(private readonly PersonalHolidayService $personalHolidayService) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(PersonalHolidayRequest $request)
    {
        try{
            $this->authorize('is-admin');

            $data = $request->validated();
            $data['date'] = $data['date'] ?? null;
            $holiday = $this->personalHolidayService->createHoliday($data);

            return response()->json([
                'message' => 'Personal holiday created successfully',
                'holiday' => new HolidayResource($holiday)
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
     * Show resource in storage.
     */
    public function show(string $id)
    {
        try {
            $holidays = $this->personalHolidayService->getUsersHolidays($id);
            return HolidayResource::collection($holidays);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PersonalHolidayRequest $request, string $id)
    {
        try{
            $this->authorize('is-admin');

            $holiday = $this->personalHolidayService->updateHolidayDate($id, $request->validated());

            return response()->json([
                'message' => 'Personal holiday updated successfully',
                'holiday' => new HolidayResource($holiday)
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

            $this->personalHolidayService->destroy($id);
            return response()->json(['message' => 'Personal holiday deleted successfully']);

        } catch (AuthorizationException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to access this resource.',
            ], 403);
        } catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }
}
