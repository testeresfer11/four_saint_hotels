<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Permission;
use Spatie\Permission\Models\Role;

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

            // User Management
            ['name' => 'user-list', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-add', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-edit', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-delete', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-view', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-change-status', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-change-subscription', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-trashed-list', 'group_name' => 'user', 'guard_name' => 'web'],
            ['name' => 'user-restore', 'group_name' => 'user', 'guard_name' => 'web'],

            // Staff Management
            ['name' => 'staff-list', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-add', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-edit', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-delete', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-view', 'group_name' => 'staff', 'guard_name' => 'web'],
            ['name' => 'staff-change-status', 'group_name' => 'staff', 'guard_name' => 'web'],

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

            // Booking Management
            ['name' => 'booking-list', 'group_name' => 'booking', 'guard_name' => 'web'],
            ['name' => 'booking-edit', 'group_name' => 'booking', 'guard_name' => 'web'],
            ['name' => 'booking-view', 'group_name' => 'booking', 'guard_name' => 'web'],
            ['name' => 'booking-cancel', 'group_name' => 'booking', 'guard_name' => 'web'],
            ['name' => 'booking-get-rooms', 'group_name' => 'booking', 'guard_name' => 'web'],

            // Hotel Management
            ['name' => 'hotel-list', 'group_name' => 'hotel', 'guard_name' => 'web'],
            ['name' => 'hotel-view', 'group_name' => 'hotel', 'guard_name' => 'web'],
            ['name' => 'hotel-upload-images', 'group_name' => 'hotel', 'guard_name' => 'web'],
            ['name' => 'hotel-image-delete', 'group_name' => 'hotel', 'guard_name' => 'web'],

            // RoomType Management
            ['name' => 'roomtype-list', 'group_name' => 'roomtype', 'guard_name' => 'web'],
            ['name' => 'roomtype-view', 'group_name' => 'roomtype', 'guard_name' => 'web'],
            ['name' => 'roomtype-upload-images', 'group_name' => 'roomtype', 'guard_name' => 'web'],
            ['name' => 'roomtype-image-delete', 'group_name' => 'roomtype', 'guard_name' => 'web'],

            // Config Setting Management
            ['name' => 'config-setting-smtp', 'group_name' => 'config-setting', 'guard_name' => 'web'],
            ['name' => 'config-setting-stripe', 'group_name' => 'config-setting', 'guard_name' => 'web'],
            ['name' => 'config-setting-config', 'group_name' => 'config-setting', 'guard_name' => 'web'],
            ['name' => 'config-setting-paypal', 'group_name' => 'config-setting', 'guard_name' => 'web'],

            // Content Pages Management
            ['name' => 'contentPages-list', 'group_name' => 'contentPages', 'guard_name' => 'web'],
            ['name' => 'contentPages-detail', 'group_name' => 'contentPages', 'guard_name' => 'web'],

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

            // Push Notification Management
            ['name' => 'pushnotification-list', 'group_name' => 'pushnotification', 'guard_name' => 'web'],
            ['name' => 'pushnotification-add', 'group_name' => 'pushnotification', 'guard_name' => 'web'],
            ['name' => 'pushnotification-edit', 'group_name' => 'pushnotification', 'guard_name' => 'web'],
            ['name' => 'pushnotification-delete', 'group_name' => 'pushnotification', 'guard_name' => 'web'],

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
            ['name' => 'vouchers-sync', 'group_name' => 'vouchers', 'guard_name' => 'web'],

            // Feedback Management
            ['name' => 'feedback-list', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-change-status', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-delete', 'group_name' => 'feedback', 'guard_name' => 'web'],
            ['name' => 'feedback-view', 'group_name' => 'feedback', 'guard_name' => 'web'],

            // Transaction Management
            ['name' => 'transaction-list', 'group_name' => 'transaction', 'guard_name' => 'web'],
            ['name' => 'transaction-view', 'group_name' => 'transaction', 'guard_name' => 'web'],

            // Chat (Twilio) Management
            ['name' => 'chat-list', 'group_name' => 'chat', 'guard_name' => 'web'],
            ['name' => 'chat-messages', 'group_name' => 'chat', 'guard_name' => 'web'],
            ['name' => 'chat-send', 'group_name' => 'chat', 'guard_name' => 'web'],
            ['name' => 'chat-conversations', 'group_name' => 'chat', 'guard_name' => 'web'],

            // Category Management
            ['name' => 'category-list', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-add', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-edit', 'group_name' => 'category', 'guard_name' => 'web'],
            ['name' => 'category-delete', 'group_name' => 'category', 'guard_name' => 'web'],

            // Other Services Management
            ['name' => 'other_services-list', 'group_name' => 'other_services', 'guard_name' => 'web'],
            ['name' => 'other_services-add', 'group_name' => 'other_services', 'guard_name' => 'web'],
            ['name' => 'other_services-edit', 'group_name' => 'other_services', 'guard_name' => 'web'],
            ['name' => 'other_services-delete', 'group_name' => 'other_services', 'guard_name' => 'web'],

            // Sub Category Management
            ['name' => 'sub_category-list', 'group_name' => 'sub_category', 'guard_name' => 'web'],
            ['name' => 'sub_category-add', 'group_name' => 'sub_category', 'guard_name' => 'web'],
            ['name' => 'sub_category-edit', 'group_name' => 'sub_category', 'guard_name' => 'web'],
            ['name' => 'sub_category-delete', 'group_name' => 'sub_category', 'guard_name' => 'web'],

            // Service Management
            ['name' => 'service-list', 'group_name' => 'service', 'guard_name' => 'web'],
            ['name' => 'service-view', 'group_name' => 'service', 'guard_name' => 'web'],

            // Payment Management
            ['name' => 'payment-list', 'group_name' => 'payment', 'guard_name' => 'web'],
            ['name' => 'payment-view', 'group_name' => 'payment', 'guard_name' => 'web'],
            ['name' => 'profile', 'group_name' => 'auth', 'guard_name' => 'web'],
            ['name' => 'changePassword', 'group_name' => 'auth', 'guard_name' => 'web'],
        ];

      $permissionModels = [];
        foreach ($permissions as $permission) {
            $permissionModels[] = Permission::updateOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name']], // unique
                ['group_name' => $permission['group_name']]
            );
        }

// Assign all permissions to admin role
    $adminRole = Role::firstOrCreate(
            ['name' => config('constants.ROLES.ADMIN'), 'guard_name' => 'web']
        );

        // Assign all permissions to admin role
        $adminRole->syncPermissions($permissionModels);

        // Assign all permissions to first admin user
        $user = User::firstWhere('role_id', $adminRole->id);
       
    }

}
