<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\ViewController;
use App\Http\Controllers\DeleteController;

Route::get('/', [UploadController::class, 'index'])->name('upload.form');
Route::post('/upload', [UploadController::class, 'store'])->middleware('throttle:uploads')->name('upload.store');
Route::get('/healthz', fn() => response('OK', 200));
Route::get('/{slug}', [ViewController::class, 'show'])->where('slug', '^(?!admin).+')->middleware('throttle:views')->name('screenshots.show');
Route::get('/{slug}/raw', [ViewController::class, 'raw'])->where('slug', '^(?!admin).+')->name('screenshots.raw');
Route::post('/{slug}/delete', [DeleteController::class, 'destroy'])->where('slug', '^(?!admin).+')->name('screenshots.delete');

Route::middleware(['admin.auth','admin.storage'])->prefix('admin/storage')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'index'])->name('admin.storage.index');
    Route::get('/create', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'create'])->name('admin.storage.create');
    Route::post('/', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'store'])->name('admin.storage.store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'edit'])->name('admin.storage.edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'update'])->name('admin.storage.update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'destroy'])->name('admin.storage.destroy');
    Route::post('/{id}/validate', [\App\Http\Controllers\Admin\StorageDestinationController::class, 'validateConnection'])->name('admin.storage.validate');
    Route::post('/test', [\App\Http\Controllers\Admin\StorageTestController::class, 'test'])->name('admin.storage.test');
});

// Admin auth
Route::get('/admin', function() { return redirect('/admin/login'); });
Route::get('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('admin.logout');

// Admin portal (requires login)
Route::middleware(['admin.auth'])->prefix('admin')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('admin.dashboard');

    // Ads settings
    Route::get('/settings/ads', [\App\Http\Controllers\Admin\SettingsController::class, 'adsForm'])->name('admin.settings.ads');
    Route::post('/settings/ads', [\App\Http\Controllers\Admin\SettingsController::class, 'saveAds'])->name('admin.settings.ads.save');

    // Monitor
    Route::get('/monitor', [\App\Http\Controllers\Admin\MonitorController::class, 'index'])->name('admin.monitor.index');
    Route::post('/purge-expired', [\App\Http\Controllers\Admin\MonitorController::class, 'purgeExpired'])->name('admin.purge');

    // User ACL
    Route::get('/users', [\App\Http\Controllers\Admin\UserAclController::class, 'index'])->name('admin.users.index');
    Route::post('/users', [\App\Http\Controllers\Admin\UserAclController::class, 'save'])->name('admin.users.save');
});

Route::post('/api/uploads/presign', [\App\Http\Controllers\Api\PresignController::class, 'presign'])->name('api.uploads.presign');
