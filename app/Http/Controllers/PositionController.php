<?php

namespace App\Http\Controllers;

use App\Http\Requests\PositionRequest;
use App\Http\Resources\PositionResource;
use App\Services\PositionService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class PositionController extends Controller
{
    public function __construct(private readonly PositionService $positionService){}

    /**
     * Get all positions.
     */
    public function index()
    {
        $positions = $this->positionService->getPositions();
        return PositionResource::collection($positions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PositionRequest $request)
    {
        try{
            $this->authorize('is-admin');

            $position = $this->positionService->createPosition($request->validated());

            return response()->json([
                'message' => 'Position created successfully.',
                'position' => new PositionResource($position)
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
    public function update(PositionRequest $request, string $id)
    {
        try{
            $this->authorize('is-admin');

            $updatedPosition = $this->positionService->updatePosition($id, $request->validated());

            return response()->json([
                'message' => 'Position updated successfully',
                'data' => new PositionResource($updatedPosition)
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

            $this->positionService->destroyPosition($id);
            return response()->json(['message' => 'Position deleted successfully']);

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
