<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;

class UserService
{
    /**
     * Update the status of a user.
     *
     * This method toggles the user's status between active (1) and inactive (0).
     * It uses the `user_id` from the incoming request to find the user and update their status.
     * 
     * @param \Illuminate\Http\Request $request The incoming request containing the user ID.
     * @return array Response containing success status and a message.
     */
    public function updateStatus($request)
    {
        try {
            // Get the user ID from the request
            $user_id = $request->user_id;

            // Find the user by their ID
            $user = User::find($user_id);
        
            // Check if the user exists, return an error message if not
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not found.',
                ];
            }

            // Retrieve the current status of the user
            $status = $user->status;

            // Toggle the status: if it's true (1), set to false (0); if false (0), set to true (1)
            $status = ($status == true) ? 0 : 1;

            // Update the user's status in the database
            $user->update(['status' => $status]);

            // Return a success response
            return [
                'success' => true,
                'message' => 'Status changed successfully.',
            ];
        
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>
