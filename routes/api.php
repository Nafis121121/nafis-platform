<?php

use App\Http\Controllers\Api\CmsApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('cms')->group(function () {
    Route::get('/chrome', [CmsApiController::class, 'chrome'])->name('api.cms.chrome');
    Route::get('/pages/{slug}', [CmsApiController::class, 'page'])->name('api.cms.page');
});