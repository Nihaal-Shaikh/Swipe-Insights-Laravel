<?php

use App\Http\Controllers\ImageController;
use App\Http\Controllers\ImageStatusController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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

Route::post('/login', [AuthController::class, 'login']);

Route::get('/images', [ImageController::class, 'getImages']);

Route::get('/image-statuses', [ImageStatusController::class, 'getImageStatus']);

Route::post('/submit-swipe-data', [ImageController::class, 'updateImageStatus']);

/* Admin Routes */

Route::get('/web-admin/images', [ImageController::class, 'listImages']);

Route::get('/web-admin/image-status', [ImageStatusController::class, 'listImageStatus']);

Route::post('/web-admin/add-image-status', [ImageStatusController::class, 'addImageStatus']);

Route::get('/web-admin/edit-image-status/{id}', [ImageStatusController::class, 'editImageStatus']);