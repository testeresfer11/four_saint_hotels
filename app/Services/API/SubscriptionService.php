<?php 
namespace App\Services\API;

use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;
use App\Models\Subscription;

class SubscriptionService
{
    /**
     * Retrieve a list of active subscriptions.
     *
     * This method fetches all subscriptions that are currently active (status = true),
     * ordered by their ID in descending order.
     *
     * @param \Illuminate\Http\Request $request
     * @return array Response indicating the success or failure of the operation, 
     *                along with the list of subscriptions or an error message.
     */
    public function getSubscriptionList($request)
    {
        try {
            // Fetch active subscriptions ordered by ID in descending order
            $subscriptions = Subscription::where('status', true)
                                         ->orderBy('id', 'desc')
                                         ->get();

            // If subscriptions are found, return success with the data
            if ($subscriptions->isNotEmpty()) {
                return [
                    'success' => true,
                    'message' => 'Subscriptions retrieved successfully',
                    'data' => $subscriptions
                ];
            }

            // If no subscriptions exist, return a failure message
            return [
                'success' => false,
                'message' => 'Subscriptions do not exist',
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
     * Allow a user to purchase a subscription.
     *
     * This method allows the authenticated user to purchase a subscription by attaching
     * the user to the selected subscription, with the provided transaction ID.
     *
     * @param \Illuminate\Http\Request $request
     * @return array Response indicating the success or failure of the operation, 
     *                along with the relevant data or an error message.
     */
    public function purchaseUserSubscription($request)
    {
        try {
            // Get authenticated user
            $user = Auth::user();
            $user_id = $user->id;

            // Retrieve subscription and transaction details from the request
            $subscription_id = $request->subscription_id;
            $transaction_id = $request->transaction_id;

            // Check if the subscription exists
            $subscription = Subscription::find($subscription_id);
            if (!$subscription) {
                return [
                    'success' => false,
                    'message' => 'Subscription does not exist',
                ];
            }

            // Check if the user has already purchased this subscription
            if ($subscription->user()->where('user_id', $user_id)->exists()) {
                return [
                    'success' => false,
                    'message' => 'You have already purchased this subscription.',
                ];
            }

            // Attach the user to the subscription with the transaction ID
            $subscription->user()->attach($user_id, ['transaction_id' => $transaction_id]);

            // Return success response with the subscription data
            return [
                'success' => true,
                'message' => 'User successfully purchased the subscription.',
                'data' => $subscription,
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
     * Retrieve the subscription details for the authenticated user.
     *
     * This method fetches the subscription information of the currently authenticated user.
     *
     * @param \Illuminate\Http\Request $request
     * @return array Response indicating the success or failure of the operation,
     *                along with the user's subscription details or an error message.
     */
    public function getUserSubscription($request)
    {
        try {
            // Get authenticated user
            $user = Auth::user();
            $user_id = $user->id;

            // Fetch the user's subscription details
            $user_subscription = User::with(['subscription'])->find($user_id);

            // If no subscription found for the user, return failure message
            if (!$user_subscription) {
                return [
                    'success' => false,
                    'message' => 'User subscription does not exist',
                ];
            }

            // Return success response with the user's subscription data
            return [
                'success' => true,
                'message' => 'User subscription retrieved successfully',
                'data' => $user_subscription
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
