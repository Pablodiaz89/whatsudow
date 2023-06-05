<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\MessageController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\LocationController;

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

 // Localidades
    Route::get('/locations', [LocationController::class, 'index'])->name('locations.index')->middleware('auth:sanctum');

// Presupuestos (mensajes)
    Route::get('/v1/budgets', [BudgetController::class, 'index'])->name('budgets.index')->middleware('auth:sanctum');
    Route::post('/v1/budgets', [BudgetController::class, 'store'])->name('budgets.store')->middleware('auth:sanctum');
    Route::get('/v1/budgets/{id}', [BudgetController::class, 'show'])->name('budgets.show')->middleware('auth:sanctum');
    Route::put('/v1/budgets/{id}', [BudgetController::class, 'update'])->name('budgets.update')->middleware('auth:sanctum');
    Route::delete('/v1/budgets/{id}', [BudgetController::class, 'destroy'])->name('budgets.destroy')->middleware('auth:sanctum');

// Controlador de mensajes (leído o no)
    Route::put('/v1/messages/{id}/read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead')->middleware('auth:sanctum'); // mensaje leíedo
    Route::get('/v1/messages/{id}/read', [MessageController::class, 'getReadStatus'])->name('messages.getReadStatus')->middleware('auth:sanctum'); // estado de lectura

 // Favoritos
    Route::post('/v1/favorites', [FavoriteController::class, 'addFavorite'])->name('favorites.add')->middleware('auth:sanctum');
    Route::delete('/v1/favorites', [FavoriteController::class, 'removeAllFavorites'])->name('favorites.remove_all')->middleware('auth:sanctum');
    Route::delete('/v1/favorites/{favoriteId}', [FavoriteController::class, 'removeSingleFavorite'])->name('favorites.remove')->middleware('auth:sanctum');
    Route::get('/v1/favorites', [FavoriteController::class, 'getFavorites'])->name('favorites.get')->middleware('auth:sanctum');

// Calendario
    Route::get('/v1/availabilities', [AvailabiltyController::class, 'index'])->name('availabilities.index')->middleware('auth:sanctum');
    Route::post('/v1/availabilities', [AvailabiltyController::class, 'store'])->name('availabilities.store')->middleware('auth:sanctum');
    Route::get('/v1/availabilities/{id}', [AvailabiltyController::class, 'show'])->name('availabilities.show')->middleware('auth:sanctum');
    Route::put('/v1/availabilities/{id}', [AvailabiltyController::class, 'update'])->name('availabilities.update')->middleware('auth:sanctum');
    Route::delete('/v1/availabilities/{id}', [AvailabiltyController::class, 'destroy'])->name('availabilities.destroy')->middleware('auth:sanctum');
    Route::get('/v1/availability-events', [AvailabiltyController::class, 'getEvents'])->name('availabilities.getEvents')->middleware('auth:sanctum');   

// Archivos
    Route::get('/v1/files', [FileController::class, 'index'])->name('files.index')->middleware('auth:sanctum'); // funciona
    Route::post('/v1/files', [FileController::class, 'store'])->name('files.store')->middleware('auth:sanctum'); // mirar como es con imagenes (campo file requerido, pero no soy capaz en postman)
    Route::get('/v1/files/{id}', [FileController::class, 'show'])->name('files.show')->middleware('auth:sanctum'); // funciona
    Route::put('/v1/files/{id}', [FileController::class, 'update'])->name('files.update')->middleware('auth:sanctum'); // mirar como es con imagenes (campo file requerido, pero no soy capaz en postman)
    Route::delete('/v1/files/{id}', [FileController::class, 'destroy'])->name('files.destroy')->middleware('auth:sanctum'); // funciona
