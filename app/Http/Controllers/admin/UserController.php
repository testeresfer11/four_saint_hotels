<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{User, UserDetail, Role};
use App\Notifications\UserNotification;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator, Hash};

class UserController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : getList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
     */
    public function getList(Request $request)
    {
        try {
            $users = User::where("role_id", 2)
                ->when($request->filled('search_keyword'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('first_name', 'like', "%$request->search_keyword%")
                            ->orWhere('last_name', 'like', "%$request->search_keyword%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->search_keyword}%"])
                            ->orWhere('email', 'like', "%$request->search_keyword%");
                    });
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })
                ->orderBy("id", "desc")->paginate(10);
            return view("admin.user.list", compact("users"));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getList**/

    /**
     * functionName : getTrashedList
     * createdDate  : 30-05-2024
     * purpose      : Get the list for all the user
     */
    public function getTrashedList(Request $request)
    {
        try {
            $users = User::where("role_id", 2)->onlyTrashed()
                ->when($request->filled('search_keyword'), function ($query) use ($request) {
                    $query->where(function ($query) use ($request) {
                        $query->where('first_name', 'like', "%$request->search_keyword%")
                            ->orWhere('last_name', 'like', "%$request->search_keyword%")
                            ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->search_keyword}%"])
                            ->orWhere('email', 'like', "%$request->search_keyword%");
                    });
                })
                ->when($request->filled('status'), function ($query) use ($request) {
                    $query->where('status', $request->status);
                })->orderBy("deleted_at", "desc")->paginate(10);
            return view("admin.user.trashed-list", compact("users"));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method getTrashedList**/

    /**
     * functionName : add
     * createdDate  : 31-05-2024
     * purpose      : add the user
     */
    public function add(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                $roles = Role::whereNotIn('name', [config('constants.ROLES.ADMIN'), config('constants.ROLES.USER')])->paginate(10);
                return view("admin.user.add", compact("roles"));
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'first_name'    => 'required|string|max:255',
                    'last_name'     => 'required|string|max:255',
                    'email'         => 'required|unique:users,email|email:rfc,dns',
                    'profile'       => 'image|max:5048',
                    'gender'        => 'required|in:Male,Female,Other',
                    'password'      => 'min:6',
                    'role_id'       => 'nullable|exists:roles,id', // <- New line for role

                ]);

                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if ($request->filled('password')) {
                    $password = $request->password;
                } else {
                    $password = generateRandomString();
                }
                $roleId = $request->role_id ?? 2; // If not selected, default to 2

                $user = User::Create([
                    'role_id'           => $roleId,
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                    'email'             => $request->email,
                    'password'          => Hash::make($password),
                    'is_email_verified' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);


                $ImgName = User::find(authId())->userDetail->profile ?? null;
                if ($request->hasFile('profile')) {
                    $ImgName = uploadFile($request->file('profile'), 'images/');
                }

                UserDetail::create([
                    'user_id'           => $user->id,
                    'phone_number'      => $request->phone_number ? $request->phone_number : '',
                    'address'           => $request->address ? $request->address : '',
                    'zip_code'          => $request->zip_code ? $request->zip_code : '',
                    'profile'           => $ImgName,
                    'gender'            => $request->gender,
                    'country_code'      => $request->country_code ? $request->country_code : '',
                    'country_short_code' => $request->country_short_code ? $request->country_short_code : '',
                    'dob'                => $request->dob ? $request->dob : '',
                ]);

                if ($request->filled('role_id')) {
                    $role = Role::find($request->role_id);
                    if ($role) {
                        $user->assignRole($role->name);
                    }
                }
                $template = $this->getTemplateByName('Account_detail');
                if ($template) {
                    $stringToReplace    = ['{{$name}}', '{{$password}}', '{{$email}}'];
                    $stringReplaceWith  = [$user->full_name, $password, $user->email];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Account_detail', $template->id);
                    $this->mailSend($emailData);
                }

                return redirect()->route('admin.user.list')->with('success', 'User ' . config('constants.SUCCESS.ADD_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method add**/

    /**
     * functionName : view
     * createdDate  : 31-05-2024
     * purpose      : Get the detail of specific user
     */
    public function view($id)
    {
        try {
            $user = User::findOrFail($id);
            return view("admin.user.view", compact("user"));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method view**/

    /**
     * functionName : edit
     * createdDate  : 31-05-2024
     * purpose      : edit the user detail
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->isMethod('get')) {
                $user = User::find($id);
                return view("admin.user.edit", compact('user'));
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'first_name'    => 'required|string|max:255',
                    'last_name'     => 'required|string|max:255',
                    'email'         => 'required|email:rfc,dns',
                    'profile'       => 'image|max:2048'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                User::where('id', $id)->update([
                    'first_name'        => $request->first_name,
                    'last_name'         => $request->last_name,
                ]);

                $user = User::find($id);
                $ImgName = $user->userDetail ? $user->userDetail->profile : '';
                if ($request->hasFile('profile')) {
                    deleteFile($ImgName, 'images/');
                    $ImgName = uploadFile($request->file('profile'), 'images/');
                }

                UserDetail::updateOrCreate(['user_id' => $id], [
                    'phone_number'      => $request->phone_number ? $request->phone_number : '',
                    'address'           => $request->address ? $request->address : '',
                    'zip_code'          => $request->zip_code ? $request->zip_code : '',
                    'profile'           => $ImgName,
                    'country_code'      => $request->country_code ? $request->country_code : '',
                    'gender'            => $request->gender ? $request->gender : '',
                    'country_short_code' => $request->country_short_code ? $request->country_short_code : '',
                    'dob'                => $request->dob ? $request->dob : '',
                ]);
                return redirect()->route('admin.user.list')->with('success', 'User ' . config('constants.SUCCESS.UPDATE_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method edit**/

    /**
     * functionName : delete
     * createdDate  : 31-05-2024
     * purpose      : Delete the user by id
     */
    public function delete($id)
    {
        try {
            User::where('id', $id)->delete();
            return response()->json(["status" => "success", "message" => "User " . config('constants.SUCCESS.DELETE_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method delete**/

    /**
     * functionName : restore
     * createdDate  : 31-05-2024
     * purpose      : Delete the user by id
     */
    public function restore($id)
    {
        try {
            User::where('id', $id)->restore();
            return response()->json(["status" => "success", "message" => "User " . config('constants.SUCCESS.RESTORE_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method restore**/

    /**
     * functionName : changeStatus
     * createdDate  : 31-05-2024
     * purpose      : Update the user status
     */
    public function changeStatus(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'id'        => 'required',
                "status"    => "required|in:0,1",
            ]);
            if ($validator->fails()) {
                if ($request->ajax()) {
                    return response()->json(["status" => "error", "message" => $validator->errors()->first()], 422);
                }
            }

            User::where('id', $request->id)->update(['status' => $request->status]);

            return response()->json(["status" => "success", "message" => "User status " . config('constants.SUCCESS.CHANGED_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method changeStatus**/

    /**
     * functionName : changeSubscription
     * createdDate  : 17-12-2024
     * purpose      : Upgrade the user premium
     */
    public function changeSubscription($id)
    {
        try {

            User::where('id', $id)->update(['plan_type' => 'premium']);

            return response()->json(["status" => "success", "message" => "User plan " . config('constants.SUCCESS.CHANGED_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method changeSubscription**/
}
