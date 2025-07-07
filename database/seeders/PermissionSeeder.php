<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;

class PermissionSeeder extends Seeder
{
       /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            ['name' => 'dashboard-view', 'group_name' => 'dashboard', 'guard_name' => 'web'],

            // Customer Management
            ['name' => 'user-list', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-add', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-edit', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-delete', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-view', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-change-status', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-trashed-list', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-restore', 'group_name' => 'user', 'guard_name' => 'web'],

              // staff Management
            ['name' => 'staff-list', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-add', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-edit', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-delete', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-view', 'group_name' => 'staff', 'guard_name' => 'web'],

            // Role Management
            ['name' => 'role-list', 'group_name' => 'role', 'guard_name' => 'web'],
            ['name' => 'role-add', 'group_name' => 'role', 'guard_name' => 'web'],
            ['name' => 'role-edit', 'group_name' => 'role', 'guard_name' => 'web'],
            ['name' => 'role-delete', 'group_name' => 'role', 'guard_name' => 'web'],
            ['name' => 'role-change-status', 'group_name' => 'role', 'guard_name' => 'web'],

            // Help Desk Management
            ['name' => 'helpdesk-list', 'group_name' => 'helpdesk', 'guard_name' => 'web'],
            ['name' => 'helpdesk-add', 'group_name' => 'helpdesk', 'guard_name' => 'web'],
            ['name' => 'helpdesk-response', 'group_name' => 'helpdesk', 'guard_name' => 'web'],
            ['name' => 'helpdesk-change-status', 'group_name' => 'helpdesk', 'guard_name' => 'web'],
            ['name' => 'helpdesk-generate-payment-link', 'group_name' => 'helpdesk', 'guard_name' => 'web'],

            // Contact Management
            ['name' => 'contact-list', 'group_name' => 'contact', 'guard_name' => 'web'],
            ['name' => 'contact-edit', 'group_name' => 'contact', 'guard_name' => 'web'],
            ['name' => 'contact-delete', 'group_name' => 'contact', 'guard_name' => 'web'],

            // Category Management
            ['name' => 'category-list', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-add', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-edit', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-delete', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-change-status', 'group_name' => 'category', 'guard_name' => 'web'],

            // Config Setting Management
            ['name' => 'config-setting-smtp', 'group_name' => 'config', 'guard_name' => 'web'],
            ['name' => 'config-setting-stripe', 'group_name' => 'config', 'guard_name' => 'web'],
            ['name' => 'config-setting-config', 'group_name' => 'config', 'guard_name' => 'web'],
            ['name' => 'config-setting-paypal', 'group_name' => 'config', 'guard_name' => 'web'],

            // Content Pages Management
            ['name' => 'contentPages-detail', 'group_name' => 'contentPage', 'guard_name' => 'web'],

            // FAQ Management
            ['name' => 'faq-list', 'group_name' => 'faq', 'guard_name' => 'web'],
            ['name' => 'faq-add', 'group_name' => 'faq', 'guard_name' => 'web'],
            ['name' => 'faq-edit', 'group_name' => 'faq', 'guard_name' => 'web'],
            ['name' => 'faq-delete', 'group_name' => 'faq', 'guard_name' => 'web'],
            ['name' => 'faq-change-status', 'group_name' => 'faq', 'guard_name' => 'web'],

            // Notification Management
            ['name' => 'notification-list', 'group_name' => 'notification', 'guard_name' => 'web'],
            ['name' => 'notification-read', 'group_name' => 'notification', 'guard_name' => 'web'],
            ['name' => 'notification-delete', 'group_name' => 'notification', 'guard_name' => 'web'],

            // Newsletter Management
            ['name' => 'newsletter-index', 'group_name' => 'newsletter', 'guard_name' => 'web'],
            ['name' => 'newsletter-delete', 'group_name' => 'newsletter', 'guard_name' => 'web'],
            ['name' => 'newsletter-change-status', 'group_name' => 'newsletter', 'guard_name' => 'web'],

            // Announcements Management
            ['name' => 'announcements-create', 'group_name' => 'announcements', 'guard_name' => 'web'],
            ['name' => 'announcements-index', 'group_name' => 'announcements', 'guard_name' => 'web'],
            ['name' => 'announcements-send', 'group_name' => 'announcements', 'guard_name' => 'web'],
            ['name' => 'announcements-delete', 'group_name' => 'announcements', 'guard_name' => 'web'],
            ['name' => 'announcements-change-status', 'group_name' => 'announcements', 'guard_name' => 'web'],

            // Vouchers Management
            ['name' => 'vouchers-list', 'group_name' => 'vouchers', 'guard_name' => 'web'],
            ['name' => 'vouchers-add', 'group_name' => 'vouchers', 'guard_name' => 'web'],
            ['name' => 'vouchers-edit', 'group_name' => 'vouchers', 'guard_name' => 'web'],
            ['name' => 'vouchers-delete', 'group_name' => 'vouchers', 'guard_name' => 'web'],
            ['name' => 'vouchers-change-status', 'group_name' => 'vouchers', 'guard_name' => 'web'],

            // Feedback Management
            ['name' => 'feedback-list', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-change-status', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-delete', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-view', 'group_name' => 'feedback', 'guard_name' => 'web'],

            // Transaction Management
            ['name' => 'transaction-list', 'group_name' => 'transaction', 'guard_name' => 'web'],
            ['name' => 'transaction-view', 'group_name' => 'transaction', 'guard_name' => 'web'],
              // Content Page Management
          

        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate($permission);
        }

        // Assign permissions to the admin role
        $role = Role::firstWhere('name', config('constants.ROLES.ADMIN'));
        $admin_permissions = Permission::pluck('name')->toArray();
        $user = User::firstWhere('role_id', $role->id);
        $user->syncPermissions($admin_permissions);
    }
}
