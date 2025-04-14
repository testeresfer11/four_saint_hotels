<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FaqController extends APIBaseController
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
    public function getAllFaq(Request $request)
    {
        try {  
            // Call the service to get the list of subscriptions
            $response = $this->faq_service->getFaqList($request);

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
