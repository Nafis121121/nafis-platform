<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CmsPreviewController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SourcingController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'show'])->name('home');

Route::get('/requests/new', function () {
    return redirect()->route('sourcing.create');
})->name('requests.create');

Route::get('/sourcing', [SourcingController::class, 'create'])->name('sourcing.create');
Route::post('/sourcing', [SourcingController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('sourcing.submit');

Route::post('/contact/send', [ContactMessageController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('contact.store');

Route::get('/cms-preview/{page}', [CmsPreviewController::class, 'show'])
    ->middleware('auth')
    ->name('cms.preview');

// Keep this single-segment CMS route last so application routes can be added safely above it.
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '[a-zA-Z0-9_\-]+');
