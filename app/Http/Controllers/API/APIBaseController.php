<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

//Helper class
use App\Helpers\ResponseHelper;

//services
use App\Services\API\RegisterService;
use App\Services\API\ProfileService;
use App\Services\API\SupportService;
use App\Services\API\NotificationsService;
use App\Services\API\PagesService;
use App\Services\API\SubscriptionService;
use App\Services\API\FaqService;


class APIBaseController extends Controller
{
    //response helper
    protected $response_helper;

    //Services
    protected $register_service;
    protected $profile_service;
    protected $support_service;
    protected $notifications_service;
    protected $pages_service;
    protected $subscription_service;
    protected $faq_service;

    public function __construct(
        ResponseHelper $response_helper,
        RegisterService $register_service,
        ProfileService $profile_service,
        SupportService $support_service,
        NotificationsService $notifications_service,
        PagesService $pages_service,
        SubscriptionService $subscription_service,
        FaqService $faq_service,
    ){
        // helper class
        $this->response_helper = $response_helper;

        // services class
        $this->register_service = $register_service;
        $this->profile_service = $profile_service;
        $this->support_service = $support_service;
        $this->notifications_service = $notifications_service;
        $this->pages_service = $pages_service;
        $this->subscription_service = $subscription_service;
        $this->faq_service = $faq_service;

        //constants
        $this->success_status = config('global-constant.STATUS_CODE.SUCCESS_STATUS');
        $this->created_status = config('global-constant.STATUS_CODE.CREATED_STATUS');
        $this->no_content_status = config('global-constant.STATUS_CODE.NO_CONTENT_STATUS');
        $this->not_found_status = config('global-constant.STATUS_CODE.NOT_FOUND_STATUS');
        $this->internal_server_status = config('global-constant.STATUS_CODE.INTERNAL_SERVER_STATUS');
        $this->unprocessable_status = config('global-constant.STATUS_CODE.UNPROCESSABLE_STATUS');
        $this->bad_request_status = config('global-constant.STATUS_CODE.BAD_REQUEST');
        $this->unauthorized_status = config('global-constant.STATUS_CODE.UNAUTHORIZED');
    }
}
