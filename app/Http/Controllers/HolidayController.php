<?php

namespace App\Http\Controllers;

use App\Http\Requests\HolidayRequest;
use App\Http\Resources\HolidayResource;
use App\Services\HolidayService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    public function __construct(private readonly HolidayService $holidayService) {}

    /**
     * Get all holidays.
     */
    public function index()
    {
        $holidays = $this->holidayService->getAll();
        return HolidayResource::collection($holidays);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(HolidayRequest $request)
    {
       try {
            $this->authorize('is-admin');

            $holiday = $this->holidayService->createHoliday($request->validated());

            return response()->json([
                'message' => 'Holiday created successfully',
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
     * Update the specified resource in storage.
     */
    public function update(HolidayRequest $request, string $id)
    {
        try{
            $this->authorize('is-admin');
        
            $updatedPosition = $this->holidayService->update($id, $request->validated());

            return response()->json([
                'message' => 'Holiday updated successfully', 
                'data' => new HolidayResource($updatedPosition)
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
            $this->authorize('is-admin');

            $this->holidayService->destroy($id);
            
            return response()->json(['message' => 'Holiday deleted successfully']);    
       
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
