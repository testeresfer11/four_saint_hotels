<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User, UserDetail, OtpManagement};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Validator, Mail, Session};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\API\SabeeHotelService;


class AuthController extends Controller
{
    use SendResponseTrait;
    protected $sabeeHotelService;

    public function __construct(sabeeHotelService $sabeeHotelService)
    {
        $this->sabeeHotelService = $sabeeHotelService;
    }

    /**
     * functionName : login
     * createdDate  : 19-06-2024
     * purpose      : logged in form submit user
     */
    public function login(Request $request, SabeeHotelService $sabeeHotelService)
    {
        try {
            if ($request->isMethod('get')) {
                if (auth()->check()) {
                    if (getRoleNameById(authId()) == config('constants.ROLES.ADMIN')) {
                        return redirect()->route('admin.dashboard');
                    }
                }
                return view('admin.auth.login');
            } else {
                $validator = Validator::make($request->all(), [
                    'email' => [
                        'required',
                        'email',
                        Rule::exists('users', 'email')->where(function ($query) {
                            $query->where('status', 1);
                        })
                    ],
                    'password' => 'required|min:8'
                ]);



                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $user = User::where('email', strtolower($request->email))->first();

                //$hotels = $sabeeHotelService->fetchAndStoreHotels();


                if (!$user->is_email_verified)
                    return redirect()->back()->with("error", 'Email not verified!');

                $role = getRoleNameById($user->id);

                if ($role == "user")
                    return redirect()->back()->with("error", 'Invalid role! You are not a ' . $role);

                $credentials = $request->only('email', 'password');
                $remember = $request->has('remember');

                if (Auth::attempt($credentials, $remember)) {
                    $user = auth()->user();

                    if ($user->two_factor_enabled) {
                        // Logout and trigger 2FA
                        Auth::logout();

                        $otp = rand(100000, 999999);

                        $user->update([
                            'two_factor_code' => $otp,
                            'two_factor_expires_at' => now()->addMinutes(10)
                        ]);

                        // Send OTP via email
                        $template = $this->getTemplateByName('Admin_OTP');

                        if ($template) {
                            $placeholders = ['{{$name}}', '{{$companyName}}', '{{$code}}', '{{YEAR}}'];
                            $replacements = ['Admin', config('app.name'), $otp, date(format: 'Y')];

                            $formattedTemplate = str_replace($placeholders, $replacements, $template->template);

                            $emailData = $this->mailData(
                                $request->email,
                                $template->subject,
                                $formattedTemplate,
                                'Admin_OTP',
                                $template->id
                            );


                            $this->mailSend($emailData);
                        }

                        session(['2fa_user_id' => $user->id]);

                        return redirect()->route('verify')->with('message', 'OTP sent to your email.');
                    }

                    return redirect()->route('admin.dashboard')->with('success', 'Login Successfully!');
                }

                return redirect()->back()->with("error", 'Invalid credentials');
            }
        } catch (\Exception $e) {

            return redirect()->back()->with("error", $e->getMessage());
        }
    }

    /**End method login**/

    /**
     * functionName : forgetPassword
     * createdDate  : 04-07-2024
     * purpose      : Forgot password
     */
    public function forgetPassword(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                return view('admin.auth.forget-password');
            } else {
                $validator = Validator::make($request->all(), [
                    'email' => [
                        'required',
                        'email',
                        Rule::exists('users', 'email')
                    ],
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $user = User::where('email', $request->email)->first();

                $this->sendOtp($request->email);

                $email = $request->email;
                session()->flash('success', 'OTP has been sent to your mail successfully');
                return view('admin.auth.verify-otp', compact('email'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method forgetPassword**/


    /**
     * functionName : forgetPassword
     * createdDate  : 04-07-2024
     * purpose      : Forgot password
     */
    public function verifyOtp(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                return view('admin.auth.verify-otp');
            }

            // POST method: handle OTP verification
            $validator = Validator::make($request->all(), [
                'email'                 => 'required|email:rfc,dns|exists:otp_management,email',
                'otp'                   => 'required|exists:otp_management,otp',
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withInput()->with('error', $validator->errors()->first());
            }

            $email = $request->email;
            if (!$email) {
                return redirect()->route('login')->with('error', 'Session expired. Please request a new code.');
            }

            $otp = OtpManagement::where('email', $request->email)
                ->where('otp', $request->otp)
                ->first();

            if (!$otp) {
                return redirect()->back()->with('error', 'Invalid email or OTP.');
            }

            $startTime = Carbon::parse($otp->updated_at);
            $finishTime = Carbon::now();
            $difference = $startTime->diffInMinutes($finishTime);

            if ($difference > 60) {
                return redirect()->back()->with('error', config('constants.ERROR.TOKEN_EXPIRED'));
            }

            $otp->delete();

            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                ['token' => $token, 'created_at' => now()]
            );

            return redirect()->route('reset-password', ['token' => $token]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**End method forgetPassword**/

    /**
     * functionName : resend
     * createdDate  : 04-07-2024
     * purpose      : resend Otp to  mail
     */
    public function resend(Request $request)
    {
        try {
            $userEmail = Session::get('otp_email'); // Store this in session when requesting OTP originally

            if (!$userEmail) {
                return redirect()->route('login')->with('error', 'Session expired. Please start over.');
            }

            $otp = rand(100000, 999999); // Generate new OTP

            // Store in session or DB as needed
            Session::put('otp_code', $otp);

            // You can use your existing mail system or Mailable class
            $this->sendOtp($userEmail);

            return back()->with('success', 'A new OTP has been sent to your email address.');
        } catch (\Exception $e) {
            return back()->with('error', 'Something went wrong while resending OTP.');
        }
    }

    /**End method resend**/

    /**
     * functionName : resetPassword
     * createdDate  : 04-07-2024
     * purpose      : Reset your password
     */
    public function resetPassword(Request $request, $token)
    {
        try {
            if ($request->isMethod('get')) {
                $reset = DB::table('password_reset_tokens')->where('token', $token)->first();
                if (!$reset)
                    return redirect()->route('login')->with('error', config('constants.ERROR.SOMETHING_WRONG'));
                $startTime = Carbon::parse($reset->created_at);
                $finishTime = Carbon::parse(now());
                $differnce = $startTime->diffInMinutes($finishTime);

                if ($differnce > 60) {
                    return redirect()->route('forget-password')->with('error', config('constants.ERROR.TOKEN_EXPIRED'));
                }
                return view('admin.auth.reset-password', compact('token'));
            } else {

                $validator = Validator::make($request->all(), [
                    "password"              => "required|confirmed|min:8",
                    "password_confirmation" => "required",
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error', $validator->errors()->first());
                }

                $reset =  DB::table('password_reset_tokens')->where('token', $token)->first();

                User::where('email', $reset->email)->update(['password' => Hash::make($request->password)]);
                DB::table('password_reset_tokens')->where('token', $token)->delete();

                return redirect()->route('login')->with('success', 'Password ' . config('constants.SUCCESS.UPDATE_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method resetPassword**/


    /**
     * functionName : profile
     * createdDate  : 30-05-2024
     * purpose      : Get and update the profile detail
     */
    public function profile(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                $user = User::with('userDetail')->find(authId());
                return view("admin.profile.detail", compact('user'));
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'full_name'    => 'required|string|max:255',
                    'email'         => 'required|email:rfc,dns',
                    'phone_number'  => 'nullable|numeric',
                    'profile'       => 'nullable|image|max:2048'
                ]);

                if ($validator->fails()) {
                    if ($request->ajax()) {
                        return response()->json([
                            "status" => "error",
                            "message" => $validator->errors()->first()
                        ], 422);
                    }
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Update User basic details
                User::where('id', authId())->update([
                    'full_name' => $request->full_name,
                    'two_factor_enabled' => $request->has('two_factor_enabled')
                ]);


                $user = User::with('userDetail')->findOrFail(authId());


                $ImgName = optional($user->userDetail)->profile;


                if ($request->hasFile('profile')) {
                    if ($ImgName) {
                        deleteFile($ImgName, 'images');
                    }
                    $ImgName = uploadFile($request->file('profile'), 'images');
                }


                UserDetail::updateOrCreate(
                    ['user_id' => authId()],
                    [
                        'phone_number'       => $request->phone_number  ?? '',
                        'address'            => $request->address       ?? '',
                        'zip_code'           => $request->zip_code      ?? '',
                        'country_code'       => $request->country_code  ?? '',
                        'country_short_code' => $request->country_short_code ?? '',
                        'dob'                => $request->dob            ?? '',
                        'profile'            => $ImgName,
                    ]


                );

                if ($request->ajax()) {
                    return response()->json([
                        "status" => "success",
                        "message" => 'Profile detail ' . config('constants.SUCCESS.UPDATE_DONE')
                    ], 200);
                }

                return redirect()->back()->with("success", 'Profile detail ' . config('constants.SUCCESS.UPDATE_DONE'));
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    "status" => "error",
                    "message" => $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with("error", $e->getMessage());
        }
    }


    /**End method profile**/

    /**
     * functionName : changePassword
     * createdDate  : 30-05-2024
     * purpose      : Get the profile detail
     */
    public function changePassword(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                return view("admin.profile.change-password");
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'current_password'  => 'required|min:8',
                    "password" => "required|confirmed|min:8",
                    "password_confirmation" => "required",
                ]);
                if ($validator->fails()) {
                    if ($request->ajax()) {
                        return response()->json(["status" => "error", "message" => $validator->errors()->first()], 422);
                    }
                }
                $user = User::find(authId());
                if ($user && Hash::check($request->current_password, $user->password)) {
                    $chagePassword = User::where("id", $user->id)->update([
                        "password" => Hash::make($request->password_confirmation)
                    ]);
                    if ($chagePassword) {
                        return response()->json(["status" => "success", "message" => "Password " . config('constants.SUCCESS.CHANGED_DONE')], 200);
                    }
                } else {
                    return response()->json([
                        'status'    => 'error',
                        "message"   => "Current Password is invalid."
                    ], 422);
                }
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(["status" => "error", $e->getMessage()], 500);
            }
            return redirect()->back()->with("error", $e->getMessage(), 500);
        }
    }
    /**End method changePassword**/


    /**
     * functionName : logout
     * createdDate  : 30-05-2024
     * purpose      : LogOut the logged in user
     */
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            return redirect()->route('login');
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method logout**/


    /**
     * functionName : show2faForm
     * createdDate  : 08-04-2025
     * purpose      : Show two factor authentication form
     */

    public function show2faForm()
    {
        return view('admin.auth.2fa');
    }

    /**End method show2faForm**/
    /**
     * functionName : verify2fa
     * createdDate  : 08-04-2025
     * purpose      : Post two factor authentication form
     */
    public function verify2fa(Request $request)
    {
        $request->validate(['code' => 'required']);

        $user = User::find(session('2fa_user_id'));

        if (
            !$user ||
            $user->two_factor_code !== $request->code ||
            \Carbon\Carbon::parse($user->two_factor_expires_at)->isPast()
        ) {
            return back()->withErrors(['code' => 'Invalid or expired OTP']);
        }

        $user->update([
            'two_factor_code' => null,
            'two_factor_expires_at' => null,
        ]);

        Auth::login($user);

        session()->forget('2fa_user_id');

        return redirect()->route('admin.dashboard')->with('success', '2FA verified successfully!');
    }
    /**End method verify2fa**/
    /**
     * functionName : sendOtp
     * createdDate  : 12-04-2025
     * purpose      : send otp email
     */
    public function sendOtp($email)
    {
        try {
            $user = User::where('email', $email)->first();
            do {
                $otp  = rand(1000, 9999);
            } while (OtpManagement::where('otp', $otp)->count());

            OtpManagement::updateOrCreate(['email' => $user->email], ['otp'   => $otp,]);

            $template = $this->getTemplateByName('Forget_password');
            if ($template) {
                $stringToReplace    = ['{{$name}}', '{{$otp}}'];
                $stringReplaceWith  = [$user->full_name, $otp];
                $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Forget_password', $template->id);
                $this->mailSend($emailData);
            }
            Session::put('otp_email', $email);
            Session::put('otp_code', $otp);

            return true;
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }

    /*end method sendOtp */
}
