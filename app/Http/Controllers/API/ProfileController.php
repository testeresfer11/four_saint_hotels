<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\UpdateUserProfileRequest;

class ProfileController extends APIBaseController
{
    /**
     * Retrieve the profile of the currently authenticated user.
     *
     * @param Request $request The HTTP request object.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user profile data or an error message.
     */
    public function profile(Request $request)
    {
        try {  
            // Call the profile_service to get the profile data of the authenticated user
            $response = $this->profile_service->getProfile($request);

            // Return the response in JSON format using the response_helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Update the profile details (name, email) of the currently authenticated user.
     *
     * @param UpdateUserProfileRequest $request The HTTP request object containing new user details.
     * @return \Illuminate\Http\JsonResponse A JSON response with the success status and updated user data or an error message.
     */
    public function updateProfile(UpdateUserProfileRequest $request)
    {
        try {  
            // Call the profile_service to update the user profile
            $response = $this->profile_service->updateUserProfile($request);

            // Return the response in JSON format using the response_helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }

    /**
     * Upload and update the profile picture of the currently authenticated user.
     *
     * @param Request $request The HTTP request object containing the new profile picture file.
     * @return \Illuminate\Http\JsonResponse A JSON response with the success status and updated user data or an error message.
     */
    public function uploadProfilePicture(Request $request)
    {
        try {  
            // Call the profile_service to upload and update the profile picture
            $response = $this->profile_service->uploadPicture($request);

            // Return the response in JSON format using the response_helper
            return $this->response_helper::jsonResponse($response, $this->success_status, $this->not_found_status);
     
        } catch (\Exception $e) {
            // Return an error response if an exception occurs
            return response()->json([
                'error' => 'Something went wrong. Please try again later.',
                'details' => $e->getMessage(), // Optional, used for debugging purposes
            ], $this->internal_server_status);
        }
    }
}
?>
