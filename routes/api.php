<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;


Route::middleware('auth:api')->group(function() {
    Route::get('/projects/{project}/next-version', [ProjectController::class, 'nextVersion']);
});