<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\V1\PdfController;
use App\Http\Controllers\Api\V1\FileController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\PhoneController;
use App\Http\Controllers\Api\V1\AvatarController;
use App\Http\Controllers\Api\V1\BudgetController;
use App\Http\Controllers\Api\V1\CompanyController;
use App\Http\Controllers\Api\V1\GalleryController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\DescriptionController;
use App\Http\Controllers\Api\V1\AvailabilityController;

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
Route::post('/registerprovider', [AuthController::class, 'registerprovider']);
Route::post('/registerorganizer', [AuthController::class, 'registerorganizer']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);

// Versión: 1
Route::group(['prefix' => 'v1', 'middleware' => 'auth:sanctum'], function () {

    // Ruta de servicios
    Route::apiResource('/services', ServiceController::class); 

    // Ruta de categorías
    Route::apiResource('/categories', CategoryController::class)->except('delete'); 

    // Ruta de compañia - nombre empresa
    Route::apiResource('/companies', CompanyController::class); 

    // Rutas de descripciones
    Route::apiResource('/descriptions', DescriptionController::class); 


    // ruta para documentos de identidad
    Route::apiResource('/documents', 'App\Http\Controllers\Api\V1\DocumentController'); 

    // Rutas de teléfonos
    Route::apiResource('/phones', 'App\Http\Controllers\Api\V1\PhoneController')->except('delete'); 

    // Rutas para el perfil del usuario (nombre, apellidos y password)
    Route::get('/user/{id}', [UserController::class, 'showProfile'])->name('profile.show'); 
    Route::put('/user/name/{id}', [UserController::class, 'updateName'])->name('profile.updateName'); 
    Route::put('/user/email/{id}', [UserController::class, 'updateEmail'])->name('profile.updateEmail');
    Route::put('/user/password/{id}', [UserController::class, 'updatePassword'])->name('profile.updatePassword');

    // Ruta de presupuesto 
    Route::apiResource('/budgets', BudgetController::class); // (tener en cuenta que los mensajes se almacenan en messages)

    // Ruta mensajes (almacenamiento)
    Route::put('/messages/{message}/mark-as-read', [MessageController::class, 'markAsRead'])->name('messages.markAsRead');
    Route::get('/messages/{message}/read-status', [MessageController::class, 'getReadStatus'])->name('messages.readStatus');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])->name('messages.reply');

    // Ruta de localizaciones (creo que no hace falta, porque  ya lo hace cuando se pide presupuesto a través de su controlador, tengo solo el metodo index para mostrar)
    Route::apiResource('locations', 'App\Http\Controllers\Api\V1\LocationController')->except(['store', 'update', 'destroy']); 

    // Favoritos
    Route::post('/favorites', [FavoriteController::class, 'addFavorite'])->name('favorites.add'); 
    Route::delete('/favorites/{favoriteId}', [FavoriteController::class, 'removeSingleFavorite'])->name('favorites.removeSingle'); 
    Route::delete('/favorites', [FavoriteController::class, 'removeAllFavorites'])->name('favorites.removeAll'); 
    Route::get('/favorites', [FavoriteController::class, 'getFavorites'])->name('favorites.get'); 


    // Avatar
    Route::get('/avatars/{id}', [AvatarController::class, 'show'])->name('avatars.show');
    Route::post('/avatars/{id}', [AvatarController::class, 'update'])->name('avatars.update');

    // Rutas de disponibilidades (calendario)
    Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
    Route::get('/availability/{id}', [AvailabilityController::class, 'show'])->name('availability.show');
    Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
    Route::put('/availability/{id}', [AvailabilityController::class, 'update'])->name('availability.update');
    Route::delete('/availability/{id}', [AvailabilityController::class, 'destroy'])->name('availability.destroy');

    // Ruta para sincronizar con Google Calendar (calendario)
    Route::get('/availability/sync-google-calendar', [AvailabilityController::class, 'syncGoogleCalendar'])->name('availability.syncGoogleCalendar');

    // Ruta para obtener los eventos de disponibilidad
    Route::get('/availability/events', [AvailabilityController::class, 'getEvents'])->name('availability.getEvents');


    //Ruta de archivos (almacenamiento)
    Route::apiResource('/files', FileController::class); // funciona

    // Ruta de Galería
    Route::apiResource('/galleries', GalleryController::class);

    // Ruta pdfs
    Route::apiResource('/pdfs', PdfController::class)->except(['create', 'edit']);
    Route::get('/pdfs/{id}/download', [PdfController::class, 'download'])->name('pdfs.download');
});
