<?php 
namespace App\Services\API;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\PersonalAccessTokenResult;
use Twilio\Rest\Client;

// Notification events
use App\Events\NewUser;

// Mail
use App\Mail\OTPMail;

// Models
use App\Models\User;
use App\Models\OTPRequest;

class RegisterService
{
    /**
     * Registers a new user, assigns a role, sends a notification, and generates an OTP.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function registerUser($request)
    {
        try{
            
            $customer_role = config('global-constant.USER_ROLES.CUSOTMER');

            // Retrieve the customer role ID
            $customerRole = Role::where('name', $customer_role)->pluck('id');
            
            if ($customerRole->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'Customer role does not exist. Please verify your role.'
                ];
            }

            // Check if the phone number already exists
            $existingUser = User::where('phone_number', $request->phone_number)->first();

            if ($existingUser) {

                if (!$existingUser->status) {
                    return [
                        'success' => false,
                        'message' => 'Your account has been blocked. Please contact our support team for assistance.'
                    ];
                }                

                OTPRequest::where( 'user_id', $existingUser->id)->delete();
                // Phone number already exists, so we generate and send OTP for the existing user
                // Generate a 4-digit OTP
                $otp = rand(1000, 9999);
                

                // Store OTP in the database with a 2-minute expiry time
                OTPRequest::create([
                    'user_id' => $existingUser->id,
                    'otp' => $otp,
                    'otp_expiry' => Carbon::now()->addMinutes(2),
                ]);

                // Send OTP via SMS
                // $this->sendOtpViaSms($request->phone_number, $otp);

                // Send OTP to the user's phone or email (depends on your application setup)

                return [
                    'success' => true,
                    'message' => 'OTP sent successfully to the registered phone number.',
                    'result' => [
                        'user'=>  $existingUser,
                        'otp'=>  $otp,
                    ]
                ];
            }

            // If the phone number doesn't exist, create a new user
            $user = User::create([
                'phone_number' => $request->phone_number,
                'phone_code' => $request->phone_code,
            ]);

            // Assign the default customer role to the new user
            $user->assignRole($customerRole);

            // Generate a 4-digit OTP for the new user
            $otp = rand(1000, 9999);

            // Store OTP in the database with a 2-minute expiry time
            OTPRequest::create([
                'user_id' => $user->id,
                'otp' => $otp,
                'otp_expiry' => Carbon::now()->addMinutes(2),
            ]);

            // Send OTP via SMS
            // $this->sendOtpViaSms($request->phone_number, $otp);

            // Trigger event notification for admin
            event(new NewUser([
                'title' => 'Customer Register',
                'notification_type' => 'register_user',
                'type' => 'customer',
                'message' => 'A new customer '.$user->phone_number.' is registered.',
                'user_id' => $user->id,
                'phone' => $user->phone_number,
            ]));       

            return [
                'success' => true,
                'message' => 'User registered successfully. Please verify your OTP.',
                'result' =>  [
                    'user'=>  $user,
                    'otp'=>  $otp,
                ]
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Send OTP via SMS using Twilio.
     */
    // private function sendOtpViaSms($phoneNumber, $otp)
    // {
    //     $sid = env('TWILIO_SID');
    //     $authToken = env('TWILIO_AUTH_TOKEN');
    //     $twilioPhoneNumber = env('TWILIO_PHONE_NUMBER');

    //     try {
    //         $client = new Client($sid, $authToken);
    //         $client->messages->create(
    //             $phoneNumber,
    //             [
    //                 'from' => $twilioPhoneNumber,
    //                 'body' => "Your OTP is: $otp"
    //             ]
    //         );
    //     } catch (\Exception $e) {
    //         // Handle any error that occurs while sending the SMS
    //         \Log::error('Error sending OTP: ' . $e->getMessage());
    //     }
    // }


    /**
     * Verifies the OTP entered by the user.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function verifyOtp($request)
    {
        try{

            // Retrieve user by phone_number
            $user = User::with('roles:id,name')
            ->where('phone_number', $request->phone_number)
            ->where('phone_code', $request->phone_code)
            ->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User does not exist.',
                ];
            }

            // Retrieve OTP entry for the user
            $otpEntry = OTPRequest::where('user_id', $user->id)
                        ->where('otp', $request->otp)
                        ->first();

            if (!$otpEntry) {
                return [
                    'success' => false,
                    'message' => 'Invalid OTP.',
                ];
            }

            // Check if OTP is expired
            if (Carbon::now()->greaterThan($otpEntry->otp_expiry)) {
                return [
                    'success' => false,
                    'message' => 'OTP has expired. Please request a new OTP.',
                ];
            }

            // Mark user as verified
            $user->is_phone_verified = true;
            $user->save();

            // Delete OTP entry after successful verification
            $otpEntry->delete();

            // Log in the user
            Auth::login($user);

                // If user has a profile picture, generate the full asset URL
            if ($user->profile_pic) {
                $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
            }
            
            // Generate token
            $token = $user->createToken('Personal Access Token')->accessToken;

            return [
                'success' => true,
                'message' => 'OTP verified successfully. You are now logged in.',
                'result' => [
                    'user' => $user,
                    'token' => $token
                ]
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Resends a new OTP if the previous one has expired.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function resendOtp($request)
    {
        try{   
            // Retrieve user by phone_number
            $user = User::where('phone_number', $request->phone_number)->first();

            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            // Generate a new 4-digit OTP
            $otp = rand(1000, 9999);

            // Update or create a new OTP entry
            OTPRequest::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp' => $otp,
                    'otp_expiry' => Carbon::now()->addMinutes(2),
                ]
            );
            
            return [
                'success' => true,
                'message' => 'A new OTP has been successfully sent to your registered mobile number.',
                'result' =>  [
                    'user'=>  $user,
                    'otp'=>  $otp,
                ]
            ];
        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
    
}
?>
