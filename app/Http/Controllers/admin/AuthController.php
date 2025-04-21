<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User,UserDetail};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash,Validator,Mail};
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : login
     * createdDate  : 19-06-2024
     * purpose      : logged in form submit user
    */
   public function login(Request $request){
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
                'email' => ['required', 'email',
                    Rule::exists('users', 'email')->where(function ($query) {
                        $query->where('status', 1);
                    })],
                'password' => 'required|min:8'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->with('error', $validator->errors()->first());
            }

            $user = User::where('email', strtolower($request->email))->first();

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
                        $placeholders = ['{{$name}}','{{$companyName}}','{{$code}}','{{YEAR}}'];
                        $replacements = ['Admin',config('app.name'),$otp,date(format: 'Y')];
                    
                        $formattedTemplate = str_replace($placeholders, $replacements, $template->template);
                    
                        $emailData = $this->mailData(
                            "preeti@yopmail.com", 
                            $template->subject, 
                            $formattedTemplate, 
                            'Admin_OTP', 
                            $template->id
                        );

                    
                        $this->mailSend($emailData);
                    }
                    
                    session(['2fa_user_id' => $user->id]);

                    return redirect()->route('admin.2fa.verify')->with('message', 'OTP sent to your email.');
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
    public function forgetPassword(Request $request){
        try{
            if($request->isMethod('get')){
                return view('admin.auth.forget-password');
            }else{
                $validator = Validator::make($request->all(), [
                    'email' => ['required','email',
                                Rule::exists('users', 'email')],
                    ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error',$validator->errors()->first());
                }

                do{
                    $token = str::random(8);;
                }while(DB::table('password_reset_tokens')->where('token',$token)->count());

                DB::table('password_reset_tokens')->where('email',$request->email)->delete();
                DB::table('password_reset_tokens')->insert(['email' => $request->email,'token' => $token,'created_at' => date('Y-m-d H:i:s')]);

                $user = User::where('email',$request->email)->first();
                $url = route('reset-password',['token'=>$token]);
                $template = $this->getTemplateByName('Web_Forget_password');
                if( $template ) { 
                    $stringToReplace    = ['{{$name}}','{{$token}}'];
                    $stringReplaceWith  = [$user->full_name,$url];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Web_Forget_password', $template->id);
                    $this->mailSend($emailData);
                }
                return redirect()->route('login')->with('success','Password reset email has been sent successfully');
            }
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method forgetPassword**/

        /**
     * functionName : resetPassword
     * createdDate  : 04-07-2024
     * purpose      : Reset your password
    */
    public function resetPassword(Request $request ,$token){
        try{
            if($request->isMethod('get')){
                $reset = DB::table('password_reset_tokens')->where('token',$token)->first();
                if(!$reset)
                    return redirect()->route('login')->with('error',config('constants.ERROR.SOMETHING_WRONG'));
                $startTime = Carbon::parse($reset->created_at);
                $finishTime = Carbon::parse(now());
                $differnce = $startTime->diffInMinutes($finishTime);
               
                if($differnce > 60){
                    return redirect()->route('forget-password')->with('error',config('constants.ERROR.TOKEN_EXPIRED'));
                }
                return view('admin.auth.reset-password',compact('token'));
            }else{

                $validator = Validator::make($request->all(), [
                    "password"              => "required|confirmed|min:8",
                    "password_confirmation" => "required",
                ]);

                if ($validator->fails()) {
                    return redirect()->back()->with('error',$validator->errors()->first());
                }

                $reset =  DB::table('password_reset_tokens')->where('token',$token)->first();

                User::where('email',$reset->email)->update(['password'=> Hash::make($request->password)]);
                DB::table('password_reset_tokens')->where('token',$token)->delete();

                return redirect()->route('login')->with('success','Password '.config('constants.SUCCESS.UPDATE_DONE'));
            }

        }catch(\Exception $e){
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
                    'first_name'    => 'required|string|max:255',
                    'last_name'     => 'required|string|max:255',
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
                    'first_name' => $request->first_name,
                    'last_name'  => $request->last_name,
                    'two_factor_enabled'=>$request->has('two_factor_enabled') 
                ]);

                // Handle image upload
                $user = User::with('userDetail')->find(authId());
                $ImgName = $user->userDetail?->profile ;

                if ($request->hasFile('profile')) {
                    if ($ImgName) {
                        deleteFile($ImgName, 'images');
                    }
                    $ImgName = uploadFile($request->file('profile'), 'images');
                }
              

                // Create or update user detail
                UserDetail::updateOrCreate(
                    ['user_id' => authId()],
                    [
                        'phone_number'       => $request->phone_number ?? '',
                        'address'            => $request->address ?? '',
                        'zip_code'           => $request->zip_code ?? '',
                        'country_code'       => $request->country_code ?? '',
                        'country_short_code' => $request->country_short_code ?? '',
                        'dob'                => $request->dob ?? '',
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
    public function changePassword(Request $request){
        try{
            if($request->isMethod('get')){
                return view("admin.profile.change-password");
            }elseif( $request->isMethod('post') ){
                $validator = Validator::make($request->all(), [
                    'current_password'  => 'required|min:8',
                    "password" => "required|confirmed|min:8",
                    "password_confirmation" => "required",
                ]);
                if ($validator->fails()) {
                    if($request->ajax()){
                        return response()->json(["status" =>"error", "message" => $validator->errors()->first()],422);
                    }
                }
                $user = User::find(authId());
                if($user && Hash::check($request->current_password, $user->password)) {
                    $chagePassword = User::where("id",$user->id)->update([
                            "password" => Hash::make($request->password_confirmation)
                        ]);
                    if($chagePassword){
                        return response()->json(["status" => "success","message" => "Password ".config('constants.SUCCESS.CHANGED_DONE')], 200);
                    }
                }else{
                    return response()->json([
                        'status'    => 'error',
                        "message"   => "Current Password is invalid."
                    ],422);
                }

            }
        }catch(\Exception $e){
            if($request->ajax()){
                return response()->json(["status" =>"error", $e->getMessage()],500);
            }
            return redirect()->back()->with("error", $e->getMessage(),500);
        }
    }
    /**End method changePassword**/


    /**
     * functionName : logout
     * createdDate  : 30-05-2024
     * purpose      : LogOut the logged in user
    */
    public function logout(Request $request){
        try{
            Auth::logout();
            return redirect()->route('login');
        }catch(\Exception $e){
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method logout**/

    public function show2faForm()
{
    return view('admin.auth.2fa');
}

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

}
