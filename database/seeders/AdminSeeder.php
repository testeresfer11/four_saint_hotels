<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
       
         $adminRole = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web'
        ]);
        

        // 3. Define modules and their actions
        $modulesWithActions = [
            'Dashboard' => ['view'],
            'User' => ['view', 'add', 'edit', 'delete'],
            'HelpDesk' => ['view', 'add', 'respond', 'changeStatus'],
            'Contact' => ['view', 'edit', 'delete'],
            'Category' => ['view', 'add', 'edit', 'delete', 'changeStatus'],
            'ConfigSetting' => ['smtp', 'stripe', 'config', 'paypal'],
            'ContentPages' => ['view', 'edit'],
            'FAQ' => ['view', 'add', 'edit', 'delete', 'changeStatus'],
            'Notification' => ['view', 'read', 'delete'],
            'Newsletter' => ['view', 'delete', 'changeStatus'],
            'Announcements' => ['create', 'view', 'send', 'delete', 'changeStatus'],
            'Vouchers' => ['view', 'add', 'edit', 'delete', 'changeStatus'],
            'Feedback' => ['view', 'delete', 'changeStatus'],
            'Transaction' => ['view'],
            'profile' => ['view', 'add', 'edit'],
             
        ];

        // 4. Create permissions and assign to admin role
        foreach ($modulesWithActions as $model => $actions) {
            foreach ($actions as $action) {
                $permission = Permission::firstOrCreate([
                    'name' => $action,
                    'module' => $model
                ]);
             $adminRole->permissions()->syncWithoutDetaching([$permission->id]);

            }
        }
        
    }
}
