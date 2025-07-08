<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');


Route::post("/users/register", [UserController::class, 'Register']);
Route::post("/users/login", [UserController::class, 'Login']);

Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::get('/users', [UserController::class, 'Get']);
    Route::patch('/users', [UserController::class, 'Update']);
    Route::post('/users/logout', [UserController::class, 'Logout']);
    // contact
    Route::post("/contact", [ContactController::class, "Create"]);
    Route::get("/contacts", [ContactController::class, "Get"]);
    Route::get("/contact/{id}", [ContactController::class, "GetById"]);
    Route::patch("/contact/{id}", [ContactController::class, "Update"]);
    Route::delete("/contact/{id}", [ContactController::class, "Delete"]);
    Route::get("/contacts/search", [ContactController::class, "Search"]);
});

