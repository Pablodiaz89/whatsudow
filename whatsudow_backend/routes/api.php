<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\CategoryController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Apartado de autenticación
    Route::post('/registerprovider', [AuthController::class, 'registerprovider']); // funciona
    Route::post('/registerorganizer', [AuthController::class, 'registerorganizer']); // funciona
    Route::post('/login', [AuthController::class, 'login']); // funciona
    Route::post('/infouser', [AuthController::class, 'infouser'])->middleware('auth:sanctum'); // la tengo para comprobar si el usuario esta autenticado
    Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Servicios
    Route::get('/v1/services', [ServiceController::class, 'index'])->name('services.index')->middleware('auth:sanctum'); // funciona
    Route::post('/v1/services', [ServiceController::class, 'store'])->name('services.store')->middleware('auth:sanctum'); // funciona
    Route::get('/v1/services/{service}', [ServiceController::class, 'show'])->name('services.show')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/services/{service}', [ServiceController::class, 'update'])->name('services.update')->middleware('auth:sanctum'); // funciona
    Route::delete('/v1/services/{service}', [ServiceController::class, 'destroy'])->name('services.destroy')->middleware('auth:sanctum'); // funciona

// Categorias
    Route::get('/v1/categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('auth:sanctum'); // funciona
    Route::post('/v1/categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('auth:sanctum'); // funciona
    Route::get('/v1/categories/{category}', [CategoryController::class, 'show'])->name('categories.show')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('auth:sanctum'); // funciona

// Perfil
    Route::get('/v1/profile/{userId}', [UserController::class, 'showProfile'])->name('profile.show')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/profile/{userId}/name', [UserController::class, 'updateName'])->name('profile.updateName')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/profile/{userId}/email', [UserController::class, 'updateEmail'])->name('profile.updateEmail')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/profile/{userId}/password', [UserController::class, 'updatePassword'])->name('profile.updatePassword')->middleware('auth:sanctum'); // tengo que mirar como hacerlo
    Route::put('/v1/profile/{userId}/phone', [UserController::class, 'updatePhone'])->name('profile.updatePhone')->middleware('auth:sanctum');
    Route::put('/v1/profile/{userId}/company', [UserController::class, 'updateCompany'])->name('profile.updateCompany')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/profile/{userId}/document', [UserController::class, 'updateDocument'])->name('profile.updateDocument')->middleware('auth:sanctum');
    Route::put('/v1/profile/{userId}/description', [UserController::class, 'updateDescription'])->name('profile.updateDescription')->middleware('auth:sanctum');
    Route::delete('/v1/profile/{userId}', [UserController::class, 'deleteAccount'])->name('profile.deleteAccount')->middleware('auth:sanctum'); // funciona