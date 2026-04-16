<?php

use App\Http\Controllers\LegacyProxyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LegacyProxyController::class, 'home']);
Route::get('/about', [LegacyProxyController::class, 'about']);
Route::get('/contact', [LegacyProxyController::class, 'contact']);
Route::get('/breaking', [LegacyProxyController::class, 'breaking']);
Route::get('/trending', [LegacyProxyController::class, 'trending']);
Route::get('/video', [LegacyProxyController::class, 'video']);
Route::get('/gallery', [LegacyProxyController::class, 'gallery']);
Route::get('/live', [LegacyProxyController::class, 'live']);
Route::get('/profile', [LegacyProxyController::class, 'profile']);
Route::get('/saved', [LegacyProxyController::class, 'saved']);
Route::get('/notifications', [LegacyProxyController::class, 'notifications']);

Route::get('/post/{slug}', [LegacyProxyController::class, 'post']);
Route::get('/category/{slug}', [LegacyProxyController::class, 'category']);
Route::get('/tag/{slug}', [LegacyProxyController::class, 'tag']);
Route::get('/search', [LegacyProxyController::class, 'search']);

Route::prefix('admin')->group(function () {
    Route::get('/', [LegacyProxyController::class, 'adminIndex']);
    Route::get('/login', [LegacyProxyController::class, 'adminLogin']);
    Route::get('/logout', [LegacyProxyController::class, 'adminLogout']);
    Route::get('/users', [LegacyProxyController::class, 'adminUsers']);
    Route::get('/ads', [LegacyProxyController::class, 'adminAds']);
    Route::get('/seo', [LegacyProxyController::class, 'adminSeo']);
    Route::get('/social', [LegacyProxyController::class, 'adminSocial']);
    Route::get('/social_dispatch', [LegacyProxyController::class, 'adminSocialDispatch']);
    Route::get('/homepage', [LegacyProxyController::class, 'adminHomepage']);
    Route::get('/logs', [LegacyProxyController::class, 'adminLogs']);

    Route::prefix('content')->group(function () {
        Route::get('/', [LegacyProxyController::class, 'adminContentIndex']);
        Route::get('/articles', [LegacyProxyController::class, 'adminContentArticles']);
        Route::get('/article_add', [LegacyProxyController::class, 'adminContentArticleAdd']);
        Route::get('/article_edit', [LegacyProxyController::class, 'adminContentArticleEdit']);
        Route::get('/article_delete', [LegacyProxyController::class, 'adminContentArticleDelete']);
        Route::get('/categories', [LegacyProxyController::class, 'adminContentCategories']);
        Route::get('/tags', [LegacyProxyController::class, 'adminContentTags']);
    });
});

