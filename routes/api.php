<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CameraController;
use App\Http\Controllers\CommonController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubscriptionPackageController;
use App\Http\Controllers\SubscriptionPackageFeatureController;
use App\Http\Controllers\UserController;
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

Route::middleware('auth:api')->group(function () {
    //refresh token and logout
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('logout', [AuthController::class, 'logout']);

    //common
    Route::get('common', [CommonController::class, 'common']);

    // permissions
    Route::get('permissions', [PermissionController::class, 'index']);
    Route::post('permission-save', [PermissionController::class, 'store']);
    Route::get('permission-by-id/{permission_id}', [PermissionController::class, 'getById']);
    Route::post('permission-update', [PermissionController::class, 'update']);
    Route::post('permission-delete', [PermissionController::class, 'destroy']);

    //Roles
    Route::get('roles', [RoleController::class, 'index']);
    Route::post('role-save', [RoleController::class, 'store']);
    Route::get('role-by-id/{role_id}', [RoleController::class, 'getById']);
    Route::post('role-update', [RoleController::class, 'update']);
    Route::post('role-delete', [RoleController::class, 'destroy']);

    // User
    Route::get('users', [UserController::class, 'index']);
    Route::post('user-save', [UserController::class, 'store']);
    Route::get('user-by-id/{role_id}', [UserController::class, 'getById']);
    Route::post('user-update', [UserController::class, 'update']);
    Route::post('user-delete', [UserController::class, 'destroy']);

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


    //Companies api

    //camera
    Route::get('camera', [CameraController::class, 'index']);
    Route::get('camera-count', [CameraController::class, 'cameraCount']);
    Route::post('camera-save', [CameraController::class, 'store']);
    Route::get('camera-by-id/{camera_id}', [CameraController::class, 'getById']);
    Route::post('camera-update', [CameraController::class, 'update']);
    Route::post('camera-status', [CameraController::class, 'status']);
    Route::post('camera-delete', [CameraController::class, 'destroy']);
});
