<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ShopifyController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\FacebookPagesController;
use App\Http\Controllers\Dashboard\MagicaiController;
use App\Http\Controllers\Dashboard\ProfileController;
use App\Http\Controllers\Dashboard\SalestrackerController;
use App\Http\Controllers\Dashboard\SalestrackerDetailsController;
use App\Http\Controllers\Dashboard\SalestrackerExplorerController;
use App\Http\Controllers\Dashboard\TiktokDetailsController;
use App\Http\Controllers\Dashboard\ToolsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password/{id}', [AuthController::class, 'resetPassword']);


// Dashboard Routes
Route::middleware(['api.user'])->group(function () {
    Route::get('list/shopify/stores', [ShopifyController::class, 'getUserStores']);
    Route::post('add/shopify/store', [ShopifyController::class, 'addStore']);
    Route::post('delete/shopify/store', [ShopifyController::class, 'deleteStore']);
    Route::post('fetch/product', [ShopifyController::class, 'fetchProduct']);
    Route::post('add/product', [ShopifyController::class, 'addProduct']);
    Route::get('dashboard', [DashboardController::class, 'indexPage']);
    Route::post('api/magic-ai', [MagicaiController::class, 'post']);
    Route::get('api/fb-ads', [DashboardController::class, 'getFbAds']);
    Route::get('api/fb-ads-winning', [DashboardController::class, 'getFbAds']); 
    Route::get('api/tt-ads', [DashboardController::class, 'getTiktokAds']);
    Route::post('ai/endpoint', [MagicaiController::class, 'checkPage']);
    Route::post('api/tools', [ToolsController::class, 'checkPage']);
    Route::get('store/ads', [SalestrackerDetailsController::class, 'getStoreAds']);
    Route::post('api/storetracker/add', [SalestrackerController::class, 'checkPage']);
    Route::post('api/salestracker/delete', [SalestrackerController::class, 'deleteStore']);
    Route::post('api/storetracker/explore', [SalestrackerExplorerController::class, 'indexPage']);
    Route::get('sales-tracker', [SalestrackerController::class, 'indexPage']);
    Route::get('sales-tracker-details/{id}', [SalestrackerDetailsController::class, 'indexPage']);
    Route::post('profile', [ProfileController::class, 'checkPage']);
    Route::get('pages', [FacebookPagesController::class, 'indexPage']);
    Route::post('deploy-from-github-webhook', [DashboardController::class, 'deploy']); // For GitHub webhook deployment
});
Route::prefix('tiktok-ads')->middleware(['api.user'])->group(function () {
    Route::get('{id}', [TiktokDetailsController::class, 'indexPage']);
});