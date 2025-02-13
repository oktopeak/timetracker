<?php

namespace App\Http\Controllers;

use App\Http\Requests\VacationRequest;
use App\Http\Resources\VacationResource;
use App\Services\VacationService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class VacationController extends Controller
{
    public function __construct(private readonly VacationService $vacationService) {}

    public function index(Request $request)
    {
        $user = $request->user();

        $vacations = $this->vacationService->getUsersVacations($user->id);

        return response()->json($vacations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VacationRequest $request)
    {
        try{
            $this->authorize('is-admin');

            $attributes = $request->validated();

            $vacation = $this->vacationService->createVacation($attributes);
            return response()->json([
                'message' => 'Vacation created successfully.',
                'team' => new VacationResource($vacation)
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
    public function update(VacationRequest $request, string $id)
    {
        try{
            $this->authorize('is-admin');

            $attributes = $request->validated();

            $vacation = $this->vacationService->updateVacation($id, $attributes);

            return response()->json([
                'message' => 'Vacation updated successfully.',
                'team' => new VacationResource($vacation)
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
}
