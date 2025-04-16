<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\{NotificationPreference, OtpManagement, Role,User, UserDetail,NotificationPreferencePermission, Plan, Subscription};
use App\Notifications\{AccountDeleteNotification,UserNotification};
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\{Auth, Hash,Validator};

class AuthController extends Controller
{
    use SendResponseTrait;
    
    /**
     * functionName : register
     * createdDate  : 03-04-2025
     * purpose      : Register the user
    */
    public function register(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'first_name'            => 'required|max:255',
                'last_name'             => 'required|max:255',
                'email'                 => 'required|unique:users,email|email:rfc,dns',
                'password'              => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
                
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }
      
            $role = Role::where('name' , config('constants.ROLES.USER'))->first();
            $user =  User::create([
                        "first_name"    => $request->first_name,
                        "last_name"     => $request->last_name,
                        "email"         => $request->email,
                        "password"      => Hash::make($request->password),
                        "role_id"       => $role->id,
                        
                    ]);

            //$token = $user->createToken('auth_token')->plainTextToken;
            
            if($user){

               
                do{
                    $otp  = rand(1000,9999);
                }while( OtpManagement::where('otp',$otp)->count());
                
                OtpManagement::updateOrCreate(['email' => $user->email],['otp'   => $otp,]);

                $template = $this->getTemplateByName('Otp_Verification');
                if( $template ) { 
                    $stringToReplace    = ['{{$name}}','{{$otp}}'];
                    $stringReplaceWith  = [$user->full_name,$otp];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Otp_Verification', $template->id);
                    $this->mailSend($emailData);
                }

              
                User::find(getAdmimId())->notify(new UserNotification($user->full_name));

                return $this->apiResponse('success',200,'User '.config('constants.SUCCESS.VERIFY_SEND'),['email' => $user->email]);
            }
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage(), $e->getLine());
        }
    }
    /*end method register */

    /**
     * functionName : verifyOtp
     * createdDate  : 03-04-2025
     * purpose      : To verify the email via otp
    */
    public function verifyOtp(Request $request){
        try{
            $validator = Validator::make($request->all(), [
                'email'                 => 'required|email:rfc,dns|exists:otp_management,email',
                'otp'                   => 'required|exists:otp_management,otp',
                'type'                  => 'required|in:otp_verify,forget_password'
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }

            $otp = OtpManagement::where(function($query) use($request){
                $query->where('email',$request->email)
                        ->where('otp',$request->otp);
            });
            if($otp->clone()->count() == 0)
                return $this->apiResponse('error',422,'Please provide valid email address or otp');

            
            $startTime = Carbon::parse($otp->clone()->first()->updated_at);
            $finishTime = Carbon::parse(now());
            $differnce = $startTime->diffInMinutes($finishTime);
           
            if($differnce > 60){
                return $this->apiResponse('error',400,config('constants.ERROR.TOKEN_EXPIRED'));
            }

            User::where('email',$request->email)->update([
                'is_email_verified' => 1,
                'email_verified_at' => date('Y-m-d H:i:s')
            ]);

            $otp->delete();

            if($request->type == 'otp_verify'){
                
                $user = User::where('email', $request->email)->first();
                Auth::login($user);
                $data = [
                    'access_token'      => $user->createToken('AuthToken')->plainTextToken,
                    'id'                => $user->id,
                    'full_name'         => $user->full_name,
                    'first_name'        => $user->first_name,
                    'last_name'         => $user->last_name,
                    'email'             => $user->email,
                    'is_verified'       => $user->is_email_verified
                    
                ];

                return $this->apiResponse('success',200,config('constants.SUCCESS.LOGIN'),$data);
            }  

            return $this->apiResponse('success',200,'User '.config('constants.SUCCESS.VERIFY_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method verifyOtp */
    
    /**
     * functionName : login
     * createdDate  : 03-04-2025
     * purpose      : login the user
    */
    public function login(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                        'email'         => 'required|email:rfc,dns|exists:users,email',
                        'password'      => 'required|min:8',
                       
                    ],[
                        'email.exists' => 'The entered email is invalid.'
                    ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }
                $credentials = $request->only(['email', 'password']);

            $user = User::where('email', $request->email)->withTrashed()->first();

            if(is_null($user)){
                return $this->apiResponse('error',400,config('constants.ERROR.SOMETHING_WRONG'));
            }
            if($user->deleted_at != null){
                return $this->apiResponse('error',400,config('constants.ERROR.DELETED_ACCOUNT'));
            }

            if($user && $user->is_email_verified == 0){
                if($user){
                    $this->sendOtp($user->email);
                }
                $data = [
                   'is_verified'       => $user->is_email_verified
                ];
                return $this->apiResponse('success',200,'User '.config('constants.SUCCESS.VERIFY_LOGIN'),$data);
            }
            if($user->status == 0)
                return $this->apiResponse('error',400,'Account is deactivated by the admin.');

            if (!Auth::attempt($credentials)) {
                return $this->apiResponse('error',400,config('constants.ERROR.INVALID_CREDENTIAL'));
            }
            
            $user                 = $request->user();
          
            $user->save();
             
            $data = [
                'access_token'      => $user->createToken('AuthToken')->plainTextToken,
                'id'                => $user->id,
                'full_name'         => $user->full_name,
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => $user->email,
                'is_verified'       => $user->is_email_verified,
                
            ];

            return $this->apiResponse('success',200,config('constants.SUCCESS.LOGIN'),$data);
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method login */

    /**
     * functionName : forgetPassword
     * createdDate  : 03-04-2025
     * purpose      : send the email for the forget password
    */
    public function forgetPassword(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'email'     => 'required|email:rfc,dns|exists:users,email',
                'type'      => 'required|in:resend_otp,forget_password'
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }
            $user = User::where('email', $request->email)->withTrashed()->first();

            if($user->deleted_at != null){
                return $this->apiResponse('error',400,config('constants.ERROR.DELETED_ACCOUNT'));
            }
            
            $this->sendOtp($request->email);
            if($request->type == 'resend_otp')
                return $this->apiResponse('success',200,'OTP '.config('constants.SUCCESS.SENT_DONE'));

            return $this->apiResponse('success',200,'Password reset email '.config('constants.SUCCESS.SENT_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method forgetPassword */

    /**
     * functionName : setNewPassword
     * createdDate  : 03-04-2025
     * purpose      : change the password
    */
    public function setNewPassword(Request $request){
        try{
            $validate = Validator::make($request->all(),[
                'email'                 => 'required|email:rfc,dns|exists:users,email',
                'password'              => 'required|confirmed|min:8',
                'password_confirmation' => 'required',
            ]);
            if ($validate->fails()) {
                return $this->apiResponse('error',422,$validate->errors()->first());
            }

            User::where('email',$request->email)->update(['password' => Hash::make($request->password)]);
            
            return $this->apiResponse('success',200,'Password '.config('constants.SUCCESS.CHANGED_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method changePassword */
    
    /**
     * functionName : logOut
     * createdDate  : 03-04-2025
     * purpose      : Logout the login user
    */
    public function logOut(Request $request){
        try{
            $user =  Auth::user();
            $user->currentAccessToken()->delete();
           
            $user->device_token = null;
            $user->device_type  = null;
            $user->save();
            
            return $this->apiResponse('success',200,config('constants.SUCCESS.LOGOUT_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method logOut */

    /**
     * functionName : profile
     * createdDate  : 03-04-2025
     * purpose      : get and update the logged in user profile
    */
    public function profile(Request $request){
        try {
            $user = Auth::user();
            if (!$user) {
                return $this->apiResponse('error', 404, 'Profile ' . config('constants.ERROR.NOT_FOUND'));
            }

            if ($request->isMethod('get')) {
                $data = new UserResource($user);
                return $this->apiResponse('success', 200, 'Profile ' . config('constants.SUCCESS.FETCH_DONE'), $data);
            }

            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'first_name'        => 'required|string|max:255',
                    'last_name'         => 'required|string|max:255',
                    'gender'            => 'nullable|in:male,female,other',
                    'birthday'          => 'nullable|date',
                    'country'           => 'nullable|string|max:255',
                    'bio'               => 'nullable|string',
                    'profile_pic'       => 'nullable|image|max:2048',

                    // From user_details table
                    'language'          => 'nullable|string|max:255',
                    'school_name'       => 'nullable|string|max:255',
                    'education_level'   => 'nullable|string|max:255',
                    'academic_year'     => 'nullable|digits:4|integer',
                    'graduating_year'   => 'nullable|digits:4|integer',
                    'program'           => 'nullable|string|max:255',
                    'major'             => 'nullable|string|max:255',
                    'minor'             => 'nullable|string|max:255',
                ]);

                if ($validator->fails()) {
                    return $this->apiResponse('error', 422, $validator->errors()->first());
                }

                // Upload profile picture if exists
                $ImgName = $user->profile_pic ?? '';
                if ($request->hasFile('profile_pic')) {
                    deleteFile($ImgName, 'images/');
                    $ImgName = uploadFile($request->file('profile_pic'), 'images/');
                }

                // Update users table
                $user->update([
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'gender'        => $request->gender,
                    'birthday'      => $request->birthday,
                    'country'       => $request->country,
                    'bio'           => $request->bio,
                    'profile_pic'   => $ImgName,
                ]);

                // Update or create user_details table
                UserDetail::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'language'          => $request->language,
                        'school_name'       => $request->school_name,
                        'education_level'   => $request->education_level,
                        'academic_year'     => $request->academic_year,
                        'graduating_year'   => $request->graduating_year,
                        'program'           => $request->program,
                        'major'             => $request->major,
                        'minor'             => $request->minor,
                    ]
                );

                $data = new UserResource($user->fresh());

                return $this->apiResponse('success', 200, 'Profile ' . config('constants.SUCCESS.UPDATE_DONE'), $data);
            }

            return $this->apiResponse('error', 405, 'Method Not Allowed');
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }

    /*end method profile */

    /**
     * functionName : changePassword
     * createdDate  : 03-04-2025
     * purpose      : change new password
    */
    public function changePassword(Request $request){
        try{
            
            $validator = Validator::make($request->all(), [
                'current_password'      => 'required|min:8',
                "password"              => "required|confirmed|min:8",
                "password_confirmation" => "required",
            ]);
            if ($validator->fails()) {
                return $this->apiResponse('error',422,$validator->errors()->first());
            }
            $user = User::find(authId());
            if($user && Hash::check($request->current_password, $user->password)) {
                $chagePassword = User::where("id",$user->id)->update([
                        "password" => Hash::make($request->password_confirmation)
                    ]);
                if($chagePassword){
                    return $this->apiResponse('success',200,"Password ".config('constants.SUCCESS.CHANGED_DONE'));
                }
            }else{
                return $this->apiResponse('error',422,"Current Password is invalid.");
            }

        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /**End method changePassword**/

    /**
     * functionName : sendOtp
     * createdDate  : 03-04-2025
     * purpose      : send otp email
    */
    public function sendOtp($email){
        try{
            $user = User::where('email',$email)->first();
            do{
                $otp  = rand(1000,9999);
            }while( OtpManagement::where('otp',$otp)->count());
            
            OtpManagement::updateOrCreate(['email' => $user->email],['otp'   => $otp,]);

            $template = $this->getTemplateByName('Forget_password');
            if( $template ) { 
                $stringToReplace    = ['{{$name}}','{{$otp}}'];
                $stringReplaceWith  = [$user->full_name,$otp];
                $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Forget_password', $template->id);
                $this->mailSend($emailData);
            }

            return true;
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /*end method sendOtp */

    /**
     * functionName : accountDelete
     * createdDate  : 03-04-2025
     * purpose      : User account deleted
    */
    public function accountDelete(){
        try{

            User::find(getAdmimId())->notify(new AccountDeleteNotification(userNameById(authId())));

            Auth::user()->delete();  

            return $this->apiResponse('success',200,"User account ".config('constants.SUCCESS.DELETE_DONE'));
        }catch(\Exception $e){
            return $this->apiResponse('error',400,$e->getMessage());
        }
    }
    /**End method changePassword**/
    

   

     
}
