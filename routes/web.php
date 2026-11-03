<?php

use App\Http\Controllers\CareTaskController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\GreenhouseController;
use App\Http\Controllers\GrowthStageController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\PlantingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [GreenhouseController::class, 'index'])
        ->name('dashboard');

    Route::post('/greenhouses', [GreenhouseController::class, 'store'])
        ->name('greenhouses.store');

    Route::get('/greenhouses/{greenhouse}', [GreenhouseController::class, 'show'])
        ->name('greenhouses.show');

    Route::delete('/greenhouses/{greenhouse}', [GreenhouseController::class, 'destroy'])
        ->name('greenhouses.destroy');

    Route::get('/crops', [CropController::class, 'index'])
        ->name('crops.index');

    Route::post('/crops', [CropController::class, 'store'])
        ->name('crops.store');

    Route::post('/greenhouses/{greenhouse}/plantings', [PlantingController::class, 'store'])
        ->name('plantings.store');

    Route::delete('/plantings/{planting}', [PlantingController::class, 'destroy'])
        ->name('plantings.destroy');

    Route::post('/plantings/{planting}/growth-stages', [GrowthStageController::class, 'store'])
        ->name('growth-stages.store');

    Route::post('/greenhouses/{greenhouse}/measurements', [MeasurementController::class, 'store'])
        ->name('measurements.store');

    Route::post('/greenhouses/{greenhouse}/care-tasks', [CareTaskController::class, 'store'])
        ->name('care-tasks.store');

    Route::patch('/care-tasks/{careTask}/complete', [CareTaskController::class, 'complete'])
        ->name('care-tasks.complete');

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';