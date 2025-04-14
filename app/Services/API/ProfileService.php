<?php 
namespace App\Services\API;

use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;
use App\Models\OTPRequest;

class ProfileService
{
    /**
     * Retrieve the profile of the currently authenticated user.
     *
     * @return array Response with the success status, message, and user data.
     */
    public function getProfile()
    {
        try {
            // Fetch the currently authenticated user
            $user = Auth::user();

            // Get user ID, or set to null if not available
            $user_id = $user->id ?? null;

            // Retrieve the user's profile along with their roles (role names only)
            $user = User::with(['roles:id,name'])->find($user_id);

            // If user has roles, remove pivot data to clean the response
            if ($user && isset($user->roles)) {
                foreach ($user->roles as $role) {
                    unset($role->pivot); // Remove pivot relationship data
                }
            }

            // If the user has a profile picture, generate a publicly accessible URL
            $user_profile = $user->profile_pic;
            if ($user_profile) {
                $user->profile_pic = asset('storage/profile/' . $user_profile);
            }

            // Return the user profile data if available
            if ($user) {
                return [
                    'success' => true,
                    'message' => 'User profile retrieved successfully',
                    'data' => $user
                ];
            }

            // Return error if user not found
            return [
                'success' => false,
                'message' => 'User not found',
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the profile details (name, email) of the currently authenticated user.
     *
     * @param $request Request data containing new user details.
     * @return array Response with the success status and updated user data.
     */
    public function updateUserProfile($request)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Check if the user is authenticated
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ];
            }

            // Update basic user details (name and email)
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone_code' => $request->phone_code,
                'phone_number' => $request->phone_number,
            ]);

            // If user has a profile picture, generate the full asset URL
            if ($user->profile_pic) {
                $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
            }

            // Return the updated user profile
            return [
                'success' => true,
                'message' => 'Profile updated successfully.',
                'data' => $user
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Upload and update the profile picture of the currently authenticated user.
     *
     * @param $request Request containing the new profile picture file.
     * @return array Response with the success status, message, and updated user data.
     */
    public function uploadPicture($request)
    {
        try {
            // Get the currently authenticated user
            $user = Auth::user();

            // Check if the user is authenticated
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'User not authenticated.',
                ];
            }

            // Handle profile picture update if provided
            if ($request->hasFile('profile_pics')) {

                // Remove existing profile picture if it exists
                if ($user->profile_pic) {
                    $profilePath = public_path('storage/profile/' . $user->profile_pic);
                    if (file_exists($profilePath)) {
                        unlink($profilePath); // Delete the old picture from storage
                    }
                }

                // Save the new profile picture with a unique name
                $fileName = uniqid() . "." . $request->file('profile_pics')->getClientOriginalExtension();
                $request->file('profile_pics')->storeAs('profile', $fileName); // Store the file in the profile folder
                $user->update([
                    'profile_pic' => $fileName, // Update the user's profile picture in the database
                ]);
            }

            // If the user has a profile picture, generate the full asset URL
            if ($user->profile_pic) {
                $user->profile_pic = asset('/storage/profile/' . $user->profile_pic);
            }

            // Return the updated user with the new profile picture
            return [
                'success' => true,
                'message' => 'Profile picture updated successfully.',
                'data' => $user
            ];

        } catch (\Exception $e) {
            // Return error response if an exception occurs
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }
}
?>
