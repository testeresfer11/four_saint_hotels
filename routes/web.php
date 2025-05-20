<?php

use App\Http\Controllers\admin\{AuthController, BookingController, ConfigSettingController, DashboardController, HelpDeskController, TransactionController, UserController, ManageFAQController, ContentPageController, NotificationController,LanguageController, ContactController, AnnouncementController,StaffController,ServiceController};
use App\Http\Controllers\admin\{GiftVoucherController,FeedbackController,RoleController,HotelController,TwilioChatController,CategoryController,ServiceSubCategoryController};
use Illuminate\Support\Facades\Route;
use App\Models\{ContentPage, ManagefAQ};
use App\Http\Controllers\NewsletterSubscriberController;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;


Route::get('/', function () {
    return redirect()->route('login');
});



// Route::fallback(function () {
//     return redirect()->route('login');
// });
Route::get('/contentPage/{slug}', [App\Http\Controllers\admin\ContentPageController::class, 'contentPage'])->name('contentPage');
Route::post('/contact-us', [App\Http\Controllers\admin\ContentPageController::class, 'storeContact'])->name('contact-us');


Route::controller(AuthController::class)->group(function () {
    Route::match(['get', 'post'], 'login', 'login')->name('login');
    Route::match(['get', 'post'], 'register', 'register')->name('register');
    Route::match(['get', 'post'], 'forget-password', 'forgetPassword')->name('forget-password');
    Route::match(['get', 'post'], 'reset-password/{token}', 'resetPassword')->name('reset-password');
    Route::match(['get', 'post'], 'verify-otp', 'verifyOtp')->name('verify');
    Route::get('resend','resend')->name('resend');


    Route::get('admin/2fa-verify', 'show2faForm')->name('admin.2fa.verify');
    Route::post('admin/2fa-verify', 'verify2fa')->name('admin.2fa.verify.post');
});
// Auth::routes();

Route::group(['prefix' => 'admin'], function () {
    Route::middleware(['auth', 'admin'])->name('admin.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/hotel/select', [DashboardController::class, 'selectHotel'])->name('hotel.select');

        // Manage auth routes
        Route::controller(AuthController::class)->group(function () {
            Route::match(['get', 'post'], 'profile', 'profile')->name('profile');
            Route::match(['get', 'post'], 'changePassword', 'changePassword')->name('changePassword');
            Route::get('logout', 'logout')->name('logout');
        });

        // Manage user routes
        Route::group(['prefix' => 'user'], function () {
            Route::name('user.')->controller(UserController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::get('view/{id}', 'view')->name('view');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
                Route::get('changeSubscription/{id}', 'changeSubscription')->name('changeSubscription');
                Route::get('trashed/list', 'getTrashedList')->name('trashed.list');
                Route::get('restore/{id}', 'restore')->name('restore');
            });

            
        });

       
            /**Manage Role routes */
            Route::group(['prefix' =>'role'],function () {
                Route::name('role.')->controller(RoleController::class)->group(function () {
                    Route::get('/','getList')->name('list');
                    Route::match(['get','post'],'add','add')->name('add');
                    Route::match(['get','post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                });
            });

            // Manage Staff routes
            Route::group(['prefix' =>'staff'],function () {
                Route::name('staff.')->controller(StaffController::class)->group(function () {
                    Route::get('list','getList')->name('list');
                    Route::match(['get', 'post'],'add','add')->name('add');
                    Route::get('view/{id}','view')->name('view');
                    Route::match(['get', 'post'],'edit/{id}','edit')->name('edit');
                    Route::get('delete/{id}','delete')->name('delete');
                    Route::get('changeStatus','changeStatus')->name('changeStatus');
                });
            });



        // Manage help desk routes
        Route::group(['prefix' => 'helpDesk'], function () {
            Route::name('helpDesk.')->controller(HelpDeskController::class)->group(function () {
                Route::get('list/{type}', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::match(['get', 'post'], 'response/{id}', 'response')->name('response');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
                Route::post('generate-payment-link', 'generatePaymentLink')->name('generatePaymentLink');
            });
        });

        // Manage help contact routes

        Route::group(['prefix' => 'contact'], function () {
            Route::name('contact.')->controller(ContactController::class)->group(function () {
                Route::get('list}', 'getList')->name('list');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');
            });
        });


        // Manage booking routes
        Route::group(['prefix' => 'booking'], function () {
            Route::name('booking.')->controller(BookingController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('view/{id}','view')->name('view');
            });
        });

        // Manage booking routes
        Route::group(['prefix' => 'hotel'], function () {
            Route::name('hotel.')->controller(HotelController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::get('view/{id}','detail')->name('view');
                Route::post('upload-images', [HotelController::class, 'uploadImages'])->name('upload.images');
                Route::get('{hotelId}/images', [HotelController::class, 'getHotelImages']);
                Route::delete('/image/delete/{id}', [HotelController::class, 'deleteHotelImage'])->name('image.delete');


            });
        });


        // Manage Config setting routes
        Route::group(['prefix' => 'config-setting'], function () {
            Route::name('config-setting.')->controller(ConfigSettingController::class)->group(function () {
                Route::match(['get', 'post'], 'smtp', 'smtpInformation')->name('smtp');
                Route::match(['get', 'post'], 'stripe', 'stripeInformation')->name('stripe');
                Route::match(['get', 'post'], 'config', 'configInformation')->name('config');
                Route::match(['get', 'post'], 'paypal', 'payPalInformation')->name('paypal');
            });
        });

        // Manage Config setting routes
        Route::group(['prefix' => 'contentPages'], function () {
            Route::name('contentPages.')->controller(ContentPageController::class)->group(function () {
                Route::match(['get', 'post'], '{slug}', 'contentPageDetail')->name('detail');
            });
        });

        /**Manage FAQ routes */
        Route::group(['prefix' => 'f-a-q'], function () {
            Route::name('f-a-q.')->controller(ManageFAQController::class)->group(function () {
                Route::get('/', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
            });
        });



        //Manage notification routes
        Route::group(['prefix' => 'notification'], function () {
            Route::name('notification.')->controller(NotificationController::class)->group(function () {
                Route::get('/', 'getList')->name('list');
                Route::get('read/{id}', 'notificationRead')->name('read');
                Route::get('delete/{id}', 'delete')->name('delete');
            });
        });

        //Manage newsletter routes
        Route::group(['prefix' => 'newsletter'], function () {
            Route::name('newsletter.')->controller(NewsletterSubscriberController::class)->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
            });
        });
        //Manage announcements routes
        Route::group(['prefix' => 'announcements'], function () {
            Route::name('announcements.')->controller(AnnouncementController::class)->group(function () {
                Route::get('create', 'create')->name('create');
                Route::get('index', 'index')->name('index');
                Route::post('send', 'send')->name('send');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('changeStatus', 'changeStatus')->name('changeStatus');
            });
        });

         // Manage help vouchers routes
            Route::group(['prefix' => 'vouchers'], function () {
                Route::name('vouchers.')->controller(GiftVoucherController::class)->group(function () {
                     Route::get('/', 'index')->name('index');
                    Route::get('/sync','sync')->name('sync');
                });
            });

        // Manage feedback routes

        Route::group(['prefix' => 'feedback'], function () {
            Route::name('feedback.')->controller(FeedbackController::class)->group(function () {
                Route::get('/', 'index')->name('list'); 
                Route::get('changeStatus','changeStatus')->name('changeStatus');
                Route::get('delete/{id}', 'delete')->name('delete');
                Route::get('view/{id}', 'show')->name('view');


            });
        });
        
        // Manage transactions routes
        Route::group(['prefix' => 'transaction'], function () {
            Route::name('transaction.')->controller(TransactionController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::get('view/{id}', 'view')->name('view');
            });
        });


        Route::group(['prefix' => 'chat'], function () {
            Route::name('chat.')->controller(TwilioChatController::class)->group(function () {
              Route::get('/','index')->name('index');
                Route::get('/{sid}/messages','getMessages')->name('messages');
                Route::post('/{sid}/send','sendMessage')->name('send');
                Route::get('/conversations', 'listConversations');

            });
        });


        Route::group(['prefix' => 'category'], function () {
            Route::name('category.')->controller(CategoryController::class)->group(function () {
               Route::get('/', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');

            });
        });
         Route::group(['prefix' => 'sub_category'], function () {
            Route::name('sub_category.')->controller(ServiceSubCategoryController::class)->group(function () {
               Route::get('/', 'getList')->name('list');
                Route::match(['get', 'post'], 'add', 'add')->name('add');
                Route::match(['get', 'post'], 'edit/{id}', 'edit')->name('edit');
                Route::get('delete/{id}', 'delete')->name('delete');

            });
        });


            

         // Manage service routes
        Route::group(['prefix' => 'service'], function () {
            Route::name('service.')->controller(ServiceController::class)->group(function () {
                Route::get('list', 'getList')->name('list');
                Route::get('view/{id}', 'view')->name('view');
            });
        });
    });
});



Route::get('/test-email', function () {
    try {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('recipi@yopmail.com')
                    ->subject('Test Email');
        });

        return 'Test email sent.';
    } catch (\Exception $e) {
        Log::error('Email sending failed: ' . $e->getMessage());
        return 'Email sending failed. Check logs for details.';
    }
});




Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
