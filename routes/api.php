<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\GalleryController;
use App\Http\Controllers\Api\ContentController;

Route::get('/dashboard/statistics', [DashboardController::class, 'statistics']);

Route::get('/news', [NewsController::class, 'index']);
Route::get('/news/{id}', [NewsController::class, 'show']);

Route::get('/galleries', [GalleryController::class, 'index']);

Route::get('/home', [ContentController::class, 'home']);
Route::get('/about', [ContentController::class, 'about']);
Route::post('/contact', [ContentController::class, 'contact']);
