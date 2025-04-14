<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Store a new subscription in the database.
     *
     * @param \Illuminate\Http\Request $request The request instance containing subscription details.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function storeSubscription($request)
    {
        try {
            // Create a new subscription record in the database
            $subscription = Subscription::create([
                'name' => $request->name,
                'android_sku_code' => $request->android_sku_code,
                'ios_sku_code' => $request->ios_sku_code,
                'description' => $request->description
            ]);

            // Return success response with the created subscription data
            return [
                'success' => true,
                'message' => 'Subscription added successfully',
                'data' => $subscription
            ];

        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Update the status of an existing subscription.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the subscription ID.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function subscriptionStatus($request)
    {
        try {
            // Retrieve the subscription using the subscription ID from the request
            $subscription_id = $request->subscription_id;
            $subscription = Subscription::find($subscription_id);
        
            if (!$subscription) {
                // If the subscription is not found, return an error message
                return [
                    'success' => false,
                    'message' => 'Subscription not found.',
                ];
            }

            // Toggle the subscription status (active -> inactive or inactive -> active)
            $status = $subscription->status;
            $status = ($status == true) ? 0 : 1;

            // Update the subscription's status in the database
            $subscription->update(['status' => $status]);

            // Return a success response after updating the status
            return [
                'success' => true,
                'message' => 'Subscription status changed successfully.',
            ];
        
        } catch (\Exception $e) {
            // Handle unexpected exceptions and return an error message
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ];
        }
    }


     /**
     * Upadte a  subscription in the database.
     *
     * @param \Illuminate\Http\Request $request The request instance containing subscription details.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function updateSubscription($request, $id){
        try {
            // Find the subscription by ID
            $subscription = Subscription::findOrFail($id);
            
            // Update the subscription with the new data
            $subscription->update([
                'name' => $request->name,
                'android_sku_code' => $request->android_sku_code,
                'ios_sku_code' => $request->ios_sku_code,
                'description' => $request->description
            ]);
            
            // Return success response with the updated subscription data
            return [
                'success' => true,
                'message' => 'Subscription updated successfully',
                'data' => $subscription
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
