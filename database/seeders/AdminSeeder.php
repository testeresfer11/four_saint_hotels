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

    $modulesWithActions = [
        'Dashboard' => ['view'],
        'User' => ['list', 'add', 'edit', 'delete', 'view', 'changeStatus', 'changeSubscription', 'trashedList', 'restore'],
        'Staff' => ['list', 'add', 'edit', 'delete', 'view', 'changeStatus'],
        'Role' => ['list', 'add', 'edit', 'delete', 'changeStatus'],
        'HelpDesk' => ['list', 'add', 'response', 'changeStatus', 'generatePaymentLink'],
        'Contact' => ['list', 'edit', 'delete'],
        'Booking' => ['list', 'edit', 'view', 'cancel', 'getRooms'],
        'Hotel' => ['list', 'view', 'uploadImages', 'imageDelete'],
        'RoomType' => ['list', 'view', 'uploadImages', 'imageDelete'],
        'ConfigSetting' => ['smtp', 'stripe', 'config', 'paypal'],
        'ContentPages' => ['list', 'detail'],
        'FAQ' => ['list', 'add', 'edit', 'delete', 'changeStatus'],
        'Notification' => ['list', 'read', 'delete'],
        'PushNotification' => ['list', 'add', 'edit', 'delete'],
        'Newsletter' => ['index', 'delete', 'changeStatus'],
        'Announcements' => ['create', 'index', 'send', 'delete', 'changeStatus'],
        'Vouchers' => ['list', 'add', 'edit', 'delete', 'sync'],
        'Feedback' => ['list', 'changeStatus', 'delete', 'view'],
        'Transaction' => ['list', 'view'],
        'Chat' => ['list', 'messages', 'send', 'conversations'],
        'Category' => ['list', 'add', 'edit', 'delete'],
        'OtherServices' => ['list', 'add', 'edit', 'delete'],
        'SubCategory' => ['list', 'add', 'edit', 'delete'],
        'Service' => ['list', 'view'],
        'Payment' => ['list', 'view'],
        'Auth' => ['profile', 'changePassword'],
    ];

    foreach ($modulesWithActions as $module => $actions) {
        foreach ($actions as $action) {
            $permission = Permission::firstOrCreate([
                'name' => strtolower($module) . '-' . $action,
                'guard_name' => 'web',
            ]);

            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }
}

}
