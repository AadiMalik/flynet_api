<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\SubscriptionPackageFeatureController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::post('login', [AuthController::class, 'login']);

Route::group(['middleware' => ['auth']], function () {

    // subscription package feature
    Route::get('subscription-package-feature', [SubscriptionPackageFeatureController::class, 'index']);
    Route::post('subscription-package-feature-save', [SubscriptionPackageFeatureController::class, 'store']);
    Route::get('subscription-package-feature-by-id/{subscription_package_feature_id}', [SubscriptionPackageFeatureController::class, 'getById']);
    Route::post('subscription-package-feature-update', [SubscriptionPackageFeatureController::class, 'update']);
    Route::post('subscription-package-feature-status', [SubscriptionPackageFeatureController::class, 'status']);
    Route::post('subscription-package-feature-delete', [SubscriptionPackageFeatureController::class, 'destroy']);

    // subscription package
    Route::get('subscription-package', [SubscriptionPackageController::class, 'index']);
    Route::post('subscription-package-save', [SubscriptionPackageController::class, 'store']);
    Route::get('subscription-package-by-id/{subscription_package_id}', [SubscriptionPackageController::class, 'getById']);
    Route::post('subscription-package-update', [SubscriptionPackageController::class, 'update']);
    Route::post('subscription-package-status', [SubscriptionPackageController::class, 'status']);
    Route::post('subscription-package-delete', [SubscriptionPackageController::class, 'destroy']);

    //business
    Route::get('business', [BusinessController::class, 'index']);
    Route::post('business-save', [BusinessController::class, 'store']);
    Route::get('business-by-id/{business_id}', [BusinessController::class, 'getById']);
    Route::post('business-update', [BusinessController::class, 'update']);
    Route::post('business-status', [BusinessController::class, 'status']);
    Route::post('business-delete', [BusinessController::class, 'destroy']);

    //locations
    Route::get('location', [LocationController::class, 'index']);
    Route::post('location-save', [LocationController::class, 'store']);
    Route::get('location-by-id/{location_id}', [LocationController::class, 'getById']);
    Route::post('location-update', [LocationController::class, 'update']);
    Route::post('location-status', [LocationController::class, 'status']);
    Route::post('location-delete', [LocationController::class, 'destroy']);
});
