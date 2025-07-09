<?php

use App\Http\Controllers\AddressController;
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
    // user
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
    // Addresses for a contact
    Route::post('/contacts/{contactId}/addresses', [AddressController::class, 'create']);
    Route::get('/contacts/{contactId}/addresses', [AddressController::class, 'list']);
    Route::get('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'get']);
    Route::put('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'update']);
    Route::delete('/contacts/{contactId}/addresses/{addressId}', [AddressController::class, 'delete']);
});

