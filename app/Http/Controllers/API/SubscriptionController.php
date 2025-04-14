<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SubscriptionController extends APIBaseController
{
    /**
     * Retrieve a list of all subscriptions.
     *
     * This method calls the SubscriptionService to fetch all active subscriptions
     * and returns them in a standardized JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with subscription data or error message.
     */
    public function getAllSubscription(Request $request)
    {
        try {  
            // Call the service to get the list of subscriptions
            $response = $this->subscription_service->getSubscriptionList($request);

            // Return the response as JSON using the response helper with appropriate status codes
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);

        } catch (\Exception $e) {
            // Handle any errors by returning a standardized error message
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, useful for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Allow the user to purchase a subscription.
     *
     * This method calls the SubscriptionService to handle the user's subscription purchase
     * and returns a response indicating success or failure.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with purchase status or error message.
     */
    public function purchaseSubscription(Request $request)
    {
        try {  
            // Call the service to handle the subscription purchase
            $response = $this->subscription_service->purchaseUserSubscription($request);

            // Return the response as JSON using the response helper with appropriate status codes
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);

        } catch (\Exception $e) {
            // Handle any errors by returning a standardized error message
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, useful for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Retrieve the subscription details for the authenticated user.
     *
     * This method calls the SubscriptionService to fetch the current subscription details
     * for the authenticated user and returns them in a standardized JSON response.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse JSON response with user's subscription data or error message.
     */
    public function userPurchaseSubscription(Request $request)
    {
        try {  
            // Call the service to get the user's subscription data
            $response = $this->subscription_service->getUserSubscription($request);

            // Return the response as JSON using the response helper with appropriate status codes
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);

        } catch (\Exception $e) {
            // Handle any errors by returning a standardized error message
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, useful for debugging
            ], $this->internal_server_status);
        }
    }
}
