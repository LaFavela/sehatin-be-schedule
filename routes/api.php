<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::apiResource("/schedules", ScheduleController::class);
