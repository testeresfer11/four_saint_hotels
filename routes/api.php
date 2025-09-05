<?php

use App\Http\Controllers\user\{AuthController, CategoryController, HelpDeskController, HomeController, SendNotificationController, NotificationController};

use App\Http\Controllers\Api\{SubjectController, FeedbackController, PostShareController, ConnectionController, PostController, ReplyController, QuickSolveController, StudyRoomController, ReviewController, SabeeHotelController, SabeeServiceController, BookingController, TwilioConversationController, TwilioVideoController,BookingPaymentController};

use App\Http\Controllers\ChatController;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


 Route::controller(BookingController::class)->group(function () {
     
        Route::get('/bookings/{id}/services/invoice','getInvoiveDataPdf');
        Route::get('/booking/invoice/{id}','downloadPdf');

    });

    Route::post('/chat/message', [ChatController::class, 'storeMessage']);
    Route::get('/chat/messages/{conversationId}', [ChatController::class, 'getMessages']);
    Route::post('/upload-chat-file', [ChatController::class, 'uploadFile']);


    Route::post('/booking/payment', [BookingPaymentController::class, 'makePayment']);
    Route::get('payment-success', [BookingPaymentController::class, 'paypalSuccess']);
    Route::get('payment-cancel', [BookingPaymentController::class, 'paypalCancel']);

    
Route::controller(HomeController::class)->group(function () {
        Route::get('contentPages/{slug}', 'contentPages');
    });

Route::controller(AuthController::class)->group(function () {
    Route::post('register', 'register');
    Route::post('verify-otp', 'verifyOtp');
    Route::post('login', 'login');
    Route::post('forget-password', 'forgetPassword');
    Route::post('set-new-password', 'setNewPassword');
    Route::post('social-login','handleSocialLogin');

});

Route::controller(SabeeHotelController::class)->group(function () {
    Route::get('/sabee/hotels/fetch', 'fetchAndStore');
     Route::get('/sabee/roomtype/fetch', 'roomFetchAndStore');
    Route::get('/sabee/hotels/detail/{id}', 'hotelDetail');
    Route::get('/sabee/hotels/room_price', 'getRoomPrice');

});




Route::controller(SabeeServiceController::class)->group(function () {
    Route::get('/sabee/service/fetch', 'fetchAndStore');
    Route::post('/sabee/submit-service', 'submitService');

});

Route::controller(BookingController::class)->group(function () {
    Route::get('/bookings', 'getBookings')->name('admin.booking.get');
     Route::get('/get-today-bookings', 'getTodayCreatedBookings')->name('admin.today_booking.get');
    Route::post('/booking/create', 'create')->name('admin.booking.create');
    Route::post('/booking/update', 'update')->name('admin.booking.update');
    Route::post('/booking/cancel', 'cancel')->name('admin.booking.cancel');
    Route::post('/booking/check-availability', 'checkAvailability')->name('admin.booking.checkAvailability');

});


Route::middleware(['auth:sanctum'])->group(function () {
    Route::controller(AuthController::class)->group(function () {
        Route::get('logout', 'logOut');
        Route::post('change-password', 'changePassword');
        Route::match(['get', 'post'], 'profile', 'Profile');
        Route::get('account/delete', 'accountDelete');
    });

    
    Route::controller(BookingController::class)->group(function () {
        Route::get('ongoing-bookings', 'getOngoingBookings')->name('ongoing.booking.get');
        Route::get('complete-bookings', 'getCompleteBookings')->name('complete.booking.get');
        Route::get('booking/{id}/detail', 'getBookingDetailById')->name('booking.detail.get');
        Route::post('/bookings/{id}/services', 'saveBookingServices');



    });


    Route::group(['prefix' => 'notification'], function () {
        Route::name('notification.')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'getNotifications')->name('list');
            Route::get('read/{id}', 'notificationRead')->name('read');
            Route::get('delete/{id}', 'delete')->name('delete');
        });
    });

    // Manage Reviews Routes

    Route::prefix('feedback')->controller(ReviewController::class)->group(function () {
        Route::get('/{id}', 'index');
        Route::get('get-feedback-by-user', 'getUserFeedback');
        Route::post('get-feedback-by-type', 'getFeedbackByType');
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
        Route::get('rooms/{roomId}', 'getRoomDetails');
        Route::get('hotels/{hotelId}/roomtype', 'getRoomTypeByHotel');
        Route::get('hotels/{hotel_id}/rooms', 'getHotelRoomTypes');
        Route::post('calculate-total','calculateTotal');
        Route::get('/hotel-coupons/{hotel_id}','getHotelCoupons');
        Route::get('get-other-services', 'getOtheServices');
        Route::get('get-facilities', 'getFacilities');



    });


    Route::prefix('twilio/conversation')->controller(TwilioConversationController::class)->group(function () {
        Route::get('create', 'createConversation');
        Route::post('add-participant', 'addParticipant');
        Route::post('send', 'sendMessage');
        Route::get('messages/{sid}', 'fetchMessages');
        Route::post('/twilio/call', 'makeCall');
    });



    Route::prefix('twilio')->controller(TwilioConversationController::class)->group(function () {

        Route::post('/call', 'makeCall');
    });
});
