<?php

namespace App\Http\Controllers;

use App\Http\Requests\ScheduleGetRequest;
use App\Http\Requests\ScheduleRequest;
use App\Http\Resources\MessageResource;
use App\Http\Resources\ScheduleResource;
use App\Models\Schedule;
use Illuminate\Http\JsonResponse;


class ScheduleController
{
    /**
     * Display a listing of the resource.
     */
    public function index(ScheduleGetRequest $request): JsonResponse
    {
        try {
            if (isset($request->validator) && $request->validator->fails()) {
                return (new MessageResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
            }
            $validatedData = $request->validated();

            $query = Schedule::query();

            if (isset($validatedData['user_id'])) {
                $query->where('user_id', '=', $validatedData['user_id']);
            }

            if (isset($validatedData['begin_date'])) {
                $query->whereDate('scheduled_at', '>=', $validatedData['begin_date']);
            }

            if (isset($validatedData['end_date'])) {
                $query->whereDate('scheduled_at', '<=', $validatedData['end_date']);
            }

            $sortBy = $validatedData['sort_by'] ?? 'created_at';
            $sortDirection = $validatedData['sort_direction'] ?? 'desc';

            $query->orderBy($sortBy, $sortDirection);

            if (isset($validatedData['per_page'])) {
                $schedule = $query->paginate($validatedData['per_page']);
                $schedule->appends($validatedData);
            } else {
                $schedule = $query->get();
            }
            if ($schedule->isEmpty()) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to get users', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(ScheduleResource::collection($schedule), true, 'Schedule data found'))->response();
    }

    /**
     * Display the specified resource.
     */
    public function show($id): JsonResponse
    {
        try {
            $user = Schedule::find($id);
            if (!$user) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to get user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new ScheduleResource($user), true, 'Schedule data found'))->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ScheduleRequest $request): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new MessageResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }

        try {
            $validated = $request->validated();
            $user = Schedule::create($validated);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to create user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new ScheduleResource($user), true, 'Schedule created successfully'))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ScheduleRequest $request, $id): JsonResponse
    {
        if (isset($request->validator) && $request->validator->fails()) {
            return (new MessageResource(null, false, 'Validation failed', $request->validator->messages()))->response()->setStatusCode(400);
        }


        try {
            $user = Schedule::find($id);
            if (!$user) {
                return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
            }
            $validated = $request->validated();
            $user->update($validated);
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to update user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new ScheduleResource($user), true, 'Schedule updated successfully'))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) : JsonResponse
    {
        $user = Schedule::find($id);
        if (!$user) {
            return (new MessageResource(null, false, 'Data not found'))->response()->setStatusCode(404);
        }

        try {
            $user->delete();
        } catch (\Exception $e) {
            return (new MessageResource(null, false, 'Failed to delete user', $e->getMessage()))->response()->setStatusCode(500);
        }
        return (new MessageResource(new ScheduleResource($user), true, 'Schedule deleted successfully'))->response();
    }
}
