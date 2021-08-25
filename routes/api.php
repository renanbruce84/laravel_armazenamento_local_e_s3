<?php

use App\Http\Controllers\ImagesController;
use App\Http\Controllers\S3ImagesController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Amazon S3 Storage
Route::get('s3', [S3ImagesController::class, 'index']);
Route::get('s3/{id}', [S3ImagesController::class, 'show']);
Route::post('s3', [S3ImagesController::class, 'store']);
Route::post('s3/{id}', [S3ImagesController::class, 'update']);
Route::delete('s3/{id}', [S3ImagesController::class, 'destroy']);

// Local Storage
Route::get('/', [ImagesController::class, 'index']);
Route::get('/{id}', [ImagesController::class, 'show']);
Route::post('/', [ImagesController::class, 'store']);
Route::post('/{id}', [ImagesController::class, 'update']);
Route::delete('/{id}', [ImagesController::class, 'destroy']);


