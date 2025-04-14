<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;


class DashboardController extends AdminBaseController
{
    public function index()
    {
        $users_count = User::count();
        return view('admin.dashboard.index',compact('users_count'));
    }
}
