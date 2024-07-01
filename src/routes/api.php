<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get("/user/index", [UserController::class, 'index']);
Route::post("/user/store", [UserController::class, 'store']);
Route::post("/user/destroy", [UserController::class, 'destroy']);
Route::post("/user/update", [UserController::class, 'update']);