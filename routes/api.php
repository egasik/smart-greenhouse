<?php

use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\StatisticsController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/crops', [CatalogController::class, 'crops']);
    Route::get('/crops/{crop}', [CatalogController::class, 'crop']);
    Route::get('/varieties', [CatalogController::class, 'varieties']);
    Route::get('/varieties/{variety}', [CatalogController::class, 'variety']);
    Route::get('/growth-stages', [CatalogController::class, 'growthStages']);
    Route::get('/statistics', StatisticsController::class);

    Route::middleware('api.key')->group(function (): void {
        Route::post('/crops', [CatalogController::class, 'storeCrop']);
        Route::match(['put', 'patch'], '/crops/{crop}', [CatalogController::class, 'updateCrop']);
        Route::delete('/crops/{crop}', [CatalogController::class, 'destroyCrop']);
        Route::post('/varieties', [CatalogController::class, 'storeVariety']);
        Route::match(['put', 'patch'], '/varieties/{variety}', [CatalogController::class, 'updateVariety']);
        Route::delete('/varieties/{variety}', [CatalogController::class, 'destroyVariety']);
    });
});
