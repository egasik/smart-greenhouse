<?php
use App\Http\Controllers\AdminCatalogController;
use App\Http\Controllers\CareTaskController;
use App\Http\Controllers\CropController;
use App\Http\Controllers\GreenhouseController;
use App\Http\Controllers\GrowthStageController;
use App\Http\Controllers\MeasurementController;
use App\Http\Controllers\PlantingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [GreenhouseController::class, 'index'])->name('dashboard');
    Route::post('/greenhouses', [GreenhouseController::class, 'store'])->name('greenhouses.store');
    Route::get('/greenhouses/{greenhouse}', [GreenhouseController::class, 'show'])->name('greenhouses.show');
    Route::patch('/greenhouses/{greenhouse}', [GreenhouseController::class, 'update'])->name('greenhouses.update');
    Route::delete('/greenhouses/{greenhouse}', [GreenhouseController::class, 'destroy'])->name('greenhouses.destroy');
    Route::get('/crops', [CropController::class, 'index'])->name('crops.index');
    Route::post('/catalog-requests', [CropController::class, 'requestStore'])->name('catalog-requests.store');
    Route::post('/greenhouses/{greenhouse}/plantings', [PlantingController::class, 'store'])->name('plantings.store');
    Route::patch('/plantings/{planting}', [PlantingController::class, 'update'])->name('plantings.update');
    Route::delete('/plantings/{planting}', [PlantingController::class, 'destroy'])->name('plantings.destroy');
    Route::post('/plantings/{planting}/growth-stages', [GrowthStageController::class, 'store'])->name('growth-stages.store');
    Route::post('/greenhouses/{greenhouse}/measurements', [MeasurementController::class, 'store'])->name('measurements.store');
    Route::patch('/measurements/{measurement}', [MeasurementController::class, 'update'])->name('measurements.update');
    Route::delete('/measurements/{measurement}', [MeasurementController::class, 'destroy'])->name('measurements.destroy');
    Route::post('/greenhouses/{greenhouse}/care-tasks', [CareTaskController::class, 'store'])->name('care-tasks.store');
    Route::patch('/care-tasks/{careTask}/complete', [CareTaskController::class, 'complete'])->name('care-tasks.complete');
    Route::patch('/care-tasks/{careTask}', [CareTaskController::class, 'update'])->name('care-tasks.update');
    Route::delete('/care-tasks/{careTask}', [CareTaskController::class, 'destroy'])->name('care-tasks.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::prefix('admin')->as('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/catalog', [AdminCatalogController::class, 'index'])->name('catalog.index');
    Route::post('/catalog', [AdminCatalogController::class, 'store'])->name('catalog.store');
    Route::patch('/catalog-requests/{catalogRequest}/approve', [AdminCatalogController::class, 'approve'])->name('catalog-requests.approve');
    Route::patch('/catalog-requests/{catalogRequest}/reject', [AdminCatalogController::class, 'reject'])->name('catalog-requests.reject');
});
require __DIR__.'/auth.php';
