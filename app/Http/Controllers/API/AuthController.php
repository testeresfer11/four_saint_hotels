<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserVerificationRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends APIBaseController
{
    /**
     * Handles user registration.
     *
     * @param RegisterRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {  
            
            $response = $this->register_service->registerUser($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->created_status); // 201 Created status
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to register user.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Verifies the OTP for user registration.
     *
     * @param UserVerificationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userVerifyOtp(UserVerificationRequest $request)
    {
        try {  

            $response = $this->register_service->verifyOtp($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->success_status);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to verify OTP.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Resends a new OTP to the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function resendUserOtp(Request $request)
    {
        try {  
            $response = $this->register_service->resendOtp($request);

            if ($response['success']) {
                return response()->json([
                    'status' => 'success',
                    'message' => $response['message'],
                    'data' => $response['result'],
                ], $this->success_status);
            } else {
                return response()->json([
                    'status' => 'failed',
                    'message' => $response['message'],
                    'data' => [],
                ], $this->bad_request_status); // 400 Bad Request status
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to resend OTP.',
                'details' => $e->getMessage(), // Optional for debugging
            ], $this->internal_server_status);
        }
    }

    /**
     * Handle user logout.
     *
     * @param Request $request The logout request.
     * @return \Illuminate\Http\JsonResponse JSON response with logout status.
     */
    public function logout(Request $request)
    {
        try {
            $user = Auth::user();
            
            if ($user) {
                // Revoke all tokens to log out user from all devices
                $user->tokens()->delete();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'User logged out successfully.',
                ], $this->success_status); // 200 OK
            }

            return response()->json([
                'status' => 'failed',
                'message' => 'User not authenticated.',
            ], $this->unauthorized_status); // 401 Unauthorized

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Failed to logout.',
                'details' => $e->getMessage(), // Optional debugging information
            ], $this->internal_server_status); // 500 Internal Server Error
        }
    }
    
}
