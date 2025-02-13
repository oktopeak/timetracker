<?php

use App\Http\Controllers\CheckInController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\LeaveTypeController;
use App\Http\Controllers\PersonalHolidayController;
use App\Http\Controllers\PositionController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VacationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function() {
    Route::post('login', [UserController::class, 'login']);
    Route::post('logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
    Route::post('user/profile-picture', [UserController::class, 'uploadProfilePicture'])->middleware('auth:sanctum');
    Route::apiResource('user', UserController::class)->middleware('auth:sanctum');
});

Route::prefix('checkins')->group(function() {
    Route::post('pin', [CheckInController::class, 'checkInPin']); //mobilna verzija
    Route::get('list', [CheckInController::class, 'getUsersCheckInsToday']); //mobilna verzija
    Route::get('last', [CheckInController::class, 'getUsersWithLastCheckIns'])->middleware('auth:sanctum');
    Route::post('', [CheckInController::class, 'checkIn'])->middleware('auth:sanctum');
    Route::get('calendar/{userId}', [CheckInController::class, 'getUsersCheckInsByYearMonth'])->middleware('auth:sanctum');
    Route::get('calendar/date-range/{userId}', [CheckInController::class, 'getCheckInsByDateRange'])->middleware('auth:sanctum');
});

Route::apiResource('leave-types', LeaveTypeController::class)->except('show')->middleware('auth:sanctum');
Route::apiResource('positions', PositionController::class)->except('show')->middleware('auth:sanctum');
Route::apiResource('vacations', VacationController::class)->only(['index','store', 'update'])->middleware('auth:sanctum');
Route::prefix('holidays')->middleware('auth:sanctum')->group(function() {
    Route::apiResource('public', HolidayController::class)->except('show');
    Route::apiResource('personal', PersonalHolidayController::class)->except('index');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::get('leaves/pending', [LeaveController::class, 'getPending']);
    Route::get('calendar/leaves-overview', [LeaveController::class, 'getLeavesOverview']);
    Route::apiResource('leave', LeaveController::class)->only(['store', 'show', 'update', 'destroy']);
    Route::get('all-leaves', [LeaveController::class, 'getLeavesForUser']);
});

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResource('teams', TeamController::class);
    Route::get('members-not', [TeamMemberController::class, 'getMembersNotInTeam']);
    Route::apiResource('team-members', TeamMemberController::class);
    Route::apiResource('tasks', TaskController::class)->except('show');
});
