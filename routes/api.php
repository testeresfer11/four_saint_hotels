<?php

use App\Http\Controllers\user\{AuthController, CategoryController, HelpDeskController, HomeController, SendNotificationController,NotificationController};

use App\Http\Controllers\Api\{SubjectController,FeedbackController, PostShareController,ConnectionController, PostController, ReplyController,QuickSolveController,StudyRoomController,ChatController,ReviewController,SabeeHotelController,SabeeServiceController,BookingController};

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

Route::controller(SabeeHotelController::class)->group(function () {
        Route::get('/sabee/hotels/fetch','fetchAndStore');
        Route::get('/sabee/hotels/detail','hotelDetail');
        
    });




Route::controller(SabeeServiceController::class)->group(function () {
        Route::get('/sabee/service/fetch','fetchAndStore');
        Route::post('/sabee/submit-service','submitService');

    });

Route::controller(BookingController::class)->group(function () {
        Route::get('/bookings','getBookings')->name('admin.booking.get');  
        Route::post('/booking/create','create')->name('admin.booking.create');
        Route::post('/booking/update','update')->name('admin.booking.update');
        Route::post('/booking/cancel','cancel')->name('admin.booking.cancel');
  

    });

Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logOut');
        Route::post('change-password', 'changePassword');
        Route::match(['get', 'post'], 'profile', 'Profile');
        Route::get('account/delete', 'accountDelete');
      
    });

    Route::controller(HomeController::class)->group(function () {
        Route::get('contentPages/{slug}', 'contentPages');
    });


   
    Route::group(['prefix' =>'notification'],function () {
        Route::name('notification.')->controller(NotificationController::class)->group(function () {
            Route::get('/','getList')->name('list');
            Route::get('read/{id}','notificationRead')->name('read');
            Route::get('delete/{id}','delete')->name('delete');
        });
    });

     // Manage Reviews Routes

    Route::prefix('feedback')->controller(ReviewController::class)->group(function () {
        Route::get('/{id}', 'index'); 
        Route::get('get-feedback-by-user', 'getUserFeedback');            
        Route::post('/add', 'store');          
        Route::post('/edit/{id}', 'update');   
        Route::get('/delete/{id}', 'destroy'); 
        
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


   Route::controller(SabeeHotelController::class)->group(function () {
        Route::get('get-hotels', 'getHotels');
        Route::get('get-hotel-detail/{id}', 'detail');
        Route::get('hotels/{hotelId}/rooms', 'getRoomsByHotel');
        Route::get('rooms/{roomId}', 'getRoomDetails');
    });



    
});



