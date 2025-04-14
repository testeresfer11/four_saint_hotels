<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\SupportController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\PagesController;
use App\Http\Controllers\API\SubscriptionController;

Route::post('login', [AuthController::class, 'register']);
Route::post('user-verify-Otp', [AuthController::class, 'userVerifyOtp']);
Route::get('resend-user-otp', [AuthController::class, 'resendUserOtp']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('update-profile', [ProfileController::class, 'updateProfile']);
    Route::post('upload-profile-picture', [ProfileController::class, 'uploadProfilePicture']);

    Route::prefix('ticket')->group(function (){
        Route::get('get-user-ticket', [SupportController::class, 'userTickets']);
        Route::post('sent-ticket', [SupportController::class, 'sentTicket']);
        Route::get('get-single-ticket', [SupportController::class, 'getSingleTicket']);
    });

    Route::prefix('notifications')->group(function(){
        Route::get('listing',[NotificationController::class,'getCustomerNotifications']);
        Route::get('recent-notifications',[NotificationController::class,'getCustomerRecentNotifications']);
        Route::get('details',[NotificationController::class,'getSingleCustomerNotification']);
    });

    Route::prefix('page')->group(function () {
        Route::get('details', [PagesController::class, 'pageDetails']);
    });

    Route::prefix('subscription')->group(function (){
        Route::get('/', [SubscriptionController::class, 'getAllSubscription']);
        Route::post('purchase', [SubscriptionController::class, 'purchaseSubscription']);
        Route::get('user-purchase-subscription', [SubscriptionController::class, 'userPurchaseSubscription']);
    });

     Route::prefix('faq')->group(function (){
        Route::get('/', [FaqController::class, 'getAllSubscription']);
       
    });

});