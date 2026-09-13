<?php

use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\CmsPreviewController;
use App\Http\Controllers\CatalogController;
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

// Public wholesale catalog. Keep these routes above the single-segment CMS fallback.
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

Route::get('/cms-preview/{page}', [CmsPreviewController::class, 'show'])
    ->middleware('auth')
    ->name('cms.preview');

Route::get('/logout', function () {
    \Illuminate\Support\Facades\Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/admin/login');
})->name('logout');

// Keep this single-segment CMS route last so application routes can be added safely above it.
Route::get('/{slug}', [PageController::class, 'show'])
    ->name('page.show')
    ->where('slug', '[a-zA-Z0-9_\-]+');
