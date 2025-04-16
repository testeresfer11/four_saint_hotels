<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\{Payment, Role, User};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * functionName : index
     * createdDate  : 29-05-2024
     * purpose      : Get the dashboard detail for the admin
     */
    public function index(){

        $role = Role::where('name' , config('constants.ROLES.USER'))->first();
        $user = User::whereNull('deleted_at')
                ->where('role_id',$role->id);
       
       
        $months = [];
        

        $responseData =[
            'total_registered_user'         => $user->clone()->count(),
            'total_active_user'             => $user->clone()->where('status',1)->count(),
            'months'                        => json_encode($months),
          

        ];
        return view("admin.dashboard",compact('responseData'));
    }
    /**End method index**/
}
