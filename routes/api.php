<?php

use App\Http\Controllers\user\{AuthController, CategoryController, HelpDeskController, HomeController, SendNotificationController};

use App\Http\Controllers\Api\{SubjectController, PostShareController,ConnectionController, PostController, ReplyController,QuickSolveController,StudyRoomController,ChatController};

use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('login', 'login');
    Route::get('forget-password', 'forgetPassword');
    Route::post('set-new-password', 'setNewPassword');
});

Route::middleware(['auth:sanctum', 'user'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logOut');
        Route::post('change-password', 'changePassword');
        Route::match(['get', 'post'], 'profile', 'Profile');
        Route::get('account/delete', 'accountDelete');
        Route::post('subscribe', 'subscribe');
        Route::post('change/theme', 'changeTheme');
    });


   


    // Manage Home Routes
    Route::controller(HomeController::class)->group(function () {
        Route::get('/home', 'home');
        Route::get('contentPages/{slug}', 'contentPages');
    });


    // Manage Help desk Routes
    Route::controller(HelpDeskController::class)->group(function () {
        Route::prefix('helpdesk')->group(function () {
            Route::get('/', 'list');
            Route::post('add', 'add');
            Route::match(['get', 'post'], 'response/{id}', 'response');
            Route::get('changestatus/{id}', 'changeStatus');
            Route::get('/subscription-ticket', 'subscriptionTicket');
        });
    });


  

    Route::controller(ChatController::class)->group(function () {
        Route::prefix('chatroom')->group(function () { 
            Route::post('save', 'create'); 
            Route::post('add-user','addUserToRoom');
            Route::post('send-message', 'saveMessage');
  
        });
    });


    

    
});



Route::controller(SendNotificationController::class)->group(function () {
    Route::get('send-notification', 'sendNotifications');
});
