<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AdminLoginController;
use App\Http\Controllers\Admin\ForgotPasswordController;
use App\Http\Controllers\Admin\ProfileSettingController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\AdminHelpSupportController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\AdminSubscriptionController;
use App\Http\Controllers\Admin\AdminFaqController;


Route::get('/', function () {
    return redirect()->route('login');
});

Route::prefix('admin')->group(function () {
    
    Route::middleware('guest_admin')->group(function () {
        Route::get('/login', [AdminLoginController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminLoginController::class, 'login'])->name('login');

        Route::get('/forgot-password', [ForgotPasswordController::class, 'forgotPasswordForm'])->name('admin-forgot-password');
        Route::post('/forgot-password', [ForgotPasswordController::class, 'forgotPassword'])->name('admin-forgot-password');
        Route::get('/reset-password/{token?}', [ForgotPasswordController::class, 'resetPasswordForm'])->name('admin-reset-password');
        Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('admin-reset-password');
    });

    Route::middleware(['isAdmin'])->group(function () {
        Route::get('/logout', [AdminLoginController::class, 'logoutAdmin'])->name('admin-logout');
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::get('profile', [ProfileSettingController::class, 'Profile'])->name('profile');
        Route::post('profile', [ProfileSettingController::class, 'updateProfile'])->name('admin-profile');

        Route::get('change-password', [ProfileSettingController::class, 'changePasswordForm'])->name('change-password');
        Route::post('change-password', [ProfileSettingController::class, 'changePassword'])->name('change-password');

        Route::prefix('user')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('user-list');
            Route::get('data', [UserController::class, 'getUserData'])->name('user-data');
            Route::get('status', [UserController::class, 'updateUserStatus'])->name('user-status');
            Route::get('details/{user_id}', [UserController::class, 'userDetails'])->name('user-details');
        });

        Route::prefix('notifications')->group(function (){
            Route::post('/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
            Route::get('/', [NotificationController::class, 'index'])->name('notification-list');
            Route::get('/details/{notification_id}', [NotificationController::class, 'notificationDetails'])->name('notification-detail');  
            Route::post('/mark-read', [NotificationController::class, 'markRead'])->name('mark-read');
        });

        Route::prefix('tickets')->group(function (){
            Route::get('/', [AdminHelpSupportController::class, 'index'])->name('tickets');
            Route::get('data', [AdminHelpSupportController::class, 'getTicketData'])->name('ticket-data');
            Route::get('details/{ticket_id}', [AdminHelpSupportController::class, 'ticketDetails'])->name('ticket-details');
            Route::post('resolved', [AdminHelpSupportController::class, 'resolvedTicket'])->name('resolved-ticket');
            Route::get('delete/{ticket_id}', [AdminHelpSupportController::class, 'deleteTicket'])->name('delete-ticket');
        });

        Route::prefix('pages')->group(function (){
            Route::get('/', [PageController::class, 'index'])->name('page-list');
            Route::get('/edit/{slug}', [PageController::class, 'editPageContent'])->name('edit-page-content');
            Route::post('/update', [PageController::class, 'update'])->name('pages.update');
        });


        Route::prefix('faq')->group(function (){
            Route::get('/', [AdminFaqController::class, 'index'])->name('faq-list');
            Route::get('data', [AdminFaqController::class, 'getFaqData'])->name('faq-data');
            Route::get('add-faq', [AdminFaqController::class, 'addFaqForm'])->name('add-faq');
            Route::post('add-faq', [AdminFaqController::class, 'addFaq'])->name('add-faq');
            Route::post('update-faq/{id}', [AdminFaqController::class, 'updateFaq'])->name('update-faq');
            Route::get('details/{faq_id}', [AdminFaqController::class, 'FaqDetails'])->name('faq-details');
            Route::get('status', [AdminFaqController::class, 'updateFaqStatus'])->name('faq-status');
            Route::get('delete-faq/{faq_id}', [AdminFaqController::class, 'deleteFaq'])->name('delete-faq');
        });



        Route::prefix('subscription')->group(function (){
            Route::get('/', [AdminSubscriptionController::class, 'index'])->name('subscription-list');
            Route::get('data', [AdminSubscriptionController::class, 'getSubscriptionData'])->name('subscription-data');
            Route::get('add-subscription', [AdminSubscriptionController::class, 'addSubscriptionForm'])->name('add-subscription');
            Route::post('add-subscription', [AdminSubscriptionController::class, 'addSubscription'])->name('add-subscription');
            Route::post('update-subscription/{id}', [AdminSubscriptionController::class, 'updateSubscription'])->name('update-subscription');
            Route::get('details/{subscription_id}', [AdminSubscriptionController::class, 'SubscriptionDetails'])->name('subscription-details');
            Route::get('status', [AdminSubscriptionController::class, 'updateSubscriptionStatus'])->name('subscription-status');
            Route::get('delete-subscription/{subscription_id}', [AdminSubscriptionController::class, 'deleteSubscription'])->name('delete-subscription');


        });
        
    });
    
});