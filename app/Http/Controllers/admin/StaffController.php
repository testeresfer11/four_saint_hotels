<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\UserDetail;
use App\Traits\SendResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    use SendResponseTrait;
    /**
     * functionName : getList
     * createdDate  : 29-11-2024
     * purpose      : Get the list for all the staff user
     */
    public function getList(Request $request){
    try {
        $role = Role::whereNotIn('name', [config('constants.ROLES.USER'), config('constants.ROLES.ADMIN')])
                    ->pluck('id')->toArray();

        $users = User::whereIn("role_id", $role)
            ->when($request->filled('search_keyword'), function ($query) use ($request) {
                $query->where(function ($query) use ($request) {
                    $query->where('full_name', 'like', "%{$request->search_keyword}%")
                        ->orWhere('email', 'like', "%{$request->search_keyword}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->when($request->filled('start_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->start_date);
            })
            ->when($request->filled('end_date'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->end_date);
            })
            ->orderBy("id", "desc")
            ->paginate(10);

        return view("admin.staff.list", compact("users"));
    } catch (\Exception $e) {
        return redirect()->back()->with("error", $e->getMessage());
    }
}

    /**End method getList**/

    /**
     * functionName : add
     * createdDate  : 29-11-2024
     * purpose      : add the staff
     */
    public function add(Request $request)
    {
        try {
            if ($request->isMethod('get')) {
                $roles = Role::whereNotIn('name', [config('constants.ROLES.ADMIN'), config('constants.ROLES.USER')])->get();
                return view("admin.staff.add", compact('roles'));
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'full_name'    => 'required|string|max:255',
                   
                    'email'         => 'required|unique:users,email|email:rfc,dns',
                    'profile'       => 'image|max:2048',
                    'gender'        => 'required|in:Male,Female,Other',
                    'status'        => 'required|in:0,1',
                    'role_id'       => 'required|exists:roles,id'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }
                if ($request->filled('password')) {
                    $password = $request->password;
                } else {
                    $password = generateRandomString();
                }

                $user = User::Create([
                    'role_id'           => $request->role_id,
                    'full_name'        => $request->full_name,
                    'email'             => $request->email,
                    'password'          => Hash::make($password),
                    'is_email_verified' => 1,
                    'email_verified_at' => date('Y-m-d H:i:s'),
                    'status'            => $request->status
                ]);

                $ImgName = User::find(authId())->userDetail->profile ?? null;
                if ($request->hasFile('profile')) {
                    $ImgName = uploadFile($request->file('profile'), 'images/');
                    $ImgName = url('storage/images/' . $ImgName);
                }

                $role = Role::findOrFail($request->role_id);
                $permissions = $role->permissions->pluck('name')->toArray();



                // Get role and permissions
                $role = Role::findOrFail($request->role_id);
                $permissions = $role->permissions->pluck('name')->toArray();
                // Assign role and permissions
                $user->assignRole($role->name);
                $user->syncPermissions($permissions);

                UserDetail::create([
                    'user_id'               => $user->id,
                    'phone_number'          => $request->phone_number ? $request->phone_number : null,
                    'address'               => $request->address ? $request->address : null,
                    'profile'               => $ImgName,
                    'gender'                => $request->gender,
                    'country_code'          => $request->country_code ? $request->country_code : null,
                    'country_short_code'    => $request->country_short_code ? $request->country_short_code : null,
                    'address2'              => $request->address2 ? $request->address2 : null,
                    'dob'                   => $request->dob ? $request->dob : null,
                ]);

                $template = $this->getTemplateByName('Account_detail');
                if ($template) {
                    $stringToReplace    = ['{{$name}}', '{{$password}}', '{{$email}}'];
                    $stringReplaceWith  = [$user->full_name, $password, $user->email];
                    $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                    $emailData          = $this->mailData($user->email, $template->subject, $newval, 'Account_detail', $template->id);
                    $this->mailSend($emailData);
                }

                return redirect()->route('admin.staff.list')->with('success', 'Staff ' . config('constants.SUCCESS.ADD_DONE'));
            }
        } catch (\Exception $e) {
            return $e->getMessage();
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method add**/

    /**
     * functionName : view
     * createdDate  : 29-11-2024
     * purpose      : Get the detail of specific staff
     */
    public function view($id)
    {
        try {
            $user = User::findOrFail($id);

            return view("admin.staff.view", compact("user"));
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method view**/

    /**
     * functionName : changeStatus
     * createdDate  : 29-11-2024
     * purpose      : Update the staff status
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

            return response()->json(["status" => "success", "message" => "Staff status " . config('constants.SUCCESS.CHANGED_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method changeStatus**/

    /**
     * functionName : edit
     * createdDate  : 29-11-2024
     * purpose      : edit the staff detail
     */
    public function edit(Request $request, $id)
    {
        try {
            if ($request->isMethod('get')) {
                $user = User::find($id);
                $roles = Role::whereNotIn('name', [config('constants.ROLES.ADMIN'), config('constants.ROLES.USER')])->get();
                return view("admin.staff.edit", compact('user', 'roles'));
            } elseif ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), [
                    'full_name'    => 'required|string|max:255',
                   
                    'email'         => 'required|email:rfc,dns',
                    'profile'       => 'image|max:2048',
                    'status'        => 'required|in:0,1',
                    'role_id'       => 'required|exists:roles,id'
                ]);
                if ($validator->fails()) {
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                $user       = User::find($id);
                $password   = $user->password;
                if ($request->filled('password')) {
                    $password = Hash::make($request->password);
                    $template = $this->getTemplateByName('password_change');
                    if ($template) {
                        $stringToReplace    = ['{{$name}}', '{{$password}}', '{{$email}}'];
                        $stringReplaceWith  = [$user->full_name, $request->password, $user->email];
                        $newval             = str_replace($stringToReplace, $stringReplaceWith, $template->template);
                        $emailData          = $this->mailData($user->email, $template->subject, $newval, 'password_change', $template->id);
                        $this->mailSend($emailData);
                    }
                }

                User::where('id', $id)->update([
                    'full_name'        => $request->full_name,
                    'status'            => $request->status,
                    'password'          => $password,
                    'role_id'           => $request->role_id
                ]);

                $user = User::find($id);
                $ImgName = $user->userDetail ? $user->userDetail->profile : null;
                if ($request->hasFile('profile')) {
                    deleteFile($ImgName, 'images/');
                    $ImgName = uploadFile($request->file('profile'), 'images/');
                    $ImgName = url('storage/images/' . $ImgName);
                }

                UserDetail::updateOrCreate(['user_id' => $id], [
                    'phone_number'      => $request->phone_number ? $request->phone_number : null,
                    'address'           => $request->address ? $request->address : null,
                    'profile'           => $ImgName,
                    'country_code'      => $request->country_code ? $request->country_code : null,
                    'country_short_code' => $request->country_short_code ? $request->country_short_code : null,
                    'address2'          => $request->address2 ? $request->address2 : null,
                    'dob'               => $request->dob ? $request->dob : null,
                ]);


                $role = Role::findOrFail($request->role_id);
                $permissions = $role->permissions->pluck('name')->toArray();
                $user->assignRole($role->name);
                $user->givePermissionTo($permissions);
                $user->syncPermissions($permissions);


                return redirect()->route('admin.staff.list')->with('success', 'Customer ' . config('constants.SUCCESS.UPDATE_DONE'));
            }
        } catch (\Exception $e) {
            return redirect()->back()->with("error", $e->getMessage());
        }
    }
    /**End method edit**/

    /**
     * functionName : delete
     * createdDate  : 29-11-2024
     * purpose      : Delete the staff by id
     */
    public function delete($id)
    {
        try {
            $user = User::find($id);

            $user->syncRoles([]);
            $user->revokePermissionTo($user->permissions);

            User::where('id', $id)->delete();

            return response()->json(["status" => "success", "message" => "Staff " . config('constants.SUCCESS.DELETE_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method delete**/
}
