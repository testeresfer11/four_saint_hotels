<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Validator};
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{ /**
    * functionName : getList
    * createdDate  : 23-07-2024
    * purpose      : Get the list for all the user
   */
   public function getList(Request $request){
       try{
           $roles = Role::whereNotIn('name',[config('constants.ROLES.ADMIN'),config('constants.ROLES.CUSTOMER'),config('constants.ROLES.DRIVER')])->paginate(10);
           return view("admin.role.list",compact("roles"));
       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method getList**/

   /**
    * functionName : add
    * createdDate  : 23-07-2024
    * purpose      : add the user
   */
   public function add(Request $request){
       try{
           if($request->isMethod('get')){
                $permissions = Permission::whereNotIn('group_name',['role'])
                ->get()->groupBy('group_name');
               return view("admin.role.add",compact('permissions'));
           }elseif( $request->isMethod('post') ){
               $validator = Validator::make($request->all(), [
                   'name'            => 'required|string|max:255',
                   'permissions'     => 'required|array',
               ]);
               
               if ($validator->fails()) {
                   return redirect()->back()->withErrors($validator)->withInput();
               }
               
               $user = Role::Create([
                   'name'        => $request->name,
               ]);

               $user->givePermissionTo( $request->permissions);

               return redirect()->route('admin.role.list')->with('success','Role'.config('constants.SUCCESS.ADD_DONE'));
           }
       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method add**/

   /**
    * functionName : edit
    * createdDate  : 23-07-2024
    * purpose      : edit the user detail
   */
   public function edit(Request $request,$id){
       try{
           if($request->isMethod('get')){
               $role = Role::find($id);
               $permissions = Permission::whereNotIn('group_name',['role'])
               ->get()->groupBy('group_name');
               return view("admin.role.edit",compact('role','permissions'));
           }elseif( $request->isMethod('post') ){
               $validator = Validator::make($request->all(), [
                   'name'            => 'required|string|max:255',
                   'permissions'     => 'required|array',
               ]);
               if ($validator->fails()) {
                   return redirect()->back()->withErrors($validator)->withInput();
               }
               Role::where('id' , $id)->update([
                   'name'        => $request->name,
               ]);

               $role = Role::findOrFail($id);
               $role->syncPermissions($request->permissions);
              
               return redirect()->route('admin.role.list')->with('success','Role '.config('constants.SUCCESS.UPDATE_DONE'));
           }
       }catch(\Exception $e){
           return redirect()->back()->with("error", $e->getMessage());
       }
   }
   /**End method edit**/

   /**
    * functionName : delete
    * createdDate  : 28-11-2024
    * purpose      : Delete the role by id
   */
   public function delete($id){
       try{
           
            $role = Role::findOrFail($id);

            $role->syncPermissions([]);

            $role->delete();

           return response()->json(["status" => "success","message" => "Role ".config('constants.SUCCESS.DELETE_DONE')], 200);
       }catch(\Exception $e){
           return response()->json(["status" =>"error", $e->getMessage()],500);
       }
   }
   /**End method delete**/

   /**
    * functionName : changeStatus
    * createdDate  : 23-07-2024
    * purpose      : Update the user status
   */
   public function changeStatus(Request $request){
       try{
           
           $validator = Validator::make($request->all(), [
               'id'        => 'required',
               "status"    => "required|in:0,1",
           ]);
           if ($validator->fails()) {
               if($request->ajax()){
                   return response()->json(["status" =>"error", "message" => $validator->errors()->first()],422);
               }
           }
           User::where('id',$request->id)->update(['status' => $request->status]);

           return response()->json(["status" => "success","message" => "User status ".config('constants.SUCCESS.CHANGED_DONE')], 200);
       }catch(\Exception $e){
           return response()->json(["status" =>"error", $e->getMessage()],500);
       }
   }
   /**End method changeStatus**/

}

