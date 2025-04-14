<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Services\Admin\LoginService;
use App\Services\Admin\ForgotPasswordService;
use App\Services\Admin\ProfileSettingService;
use App\Services\Admin\UserService;
use App\Services\Admin\SupportTicketService;
use App\Services\Admin\SubscriptionService;
use App\Services\Admin\FaqService;
class AdminBaseController extends Controller
{
    //Services
    protected $login_service;
    protected $forgot_password_service;
    protected $profile_setting_service;
    protected $user_service;
    protected $support_ticket_service;
    protected $subscription_service;
    protected $faq_service;


    public function __construct(
        LoginService $login_service,
        ForgotPasswordService $forgot_password_service,
        ProfileSettingService $profile_setting_service,
        UserService $user_service,
        SupportTicketService $support_ticket_service,
        SubscriptionService $subscription_service,
        FaqService $faq_service
    )
    {
        // services class
        $this->login_service = $login_service;
        $this->forgot_password_service = $forgot_password_service;
        $this->profile_setting_service = $profile_setting_service;
        $this->user_service = $user_service;
        $this->support_ticket_service = $support_ticket_service;
        $this->subscription_service = $subscription_service;
        $this->faq_service = $faq_service;
    }
}
