<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Subscription;

class AdminSubscriptionController extends AdminBaseController
{
    /**
     * Display the subscription index page.
     *
     * @return \Illuminate\View\View The view for the subscription index page.
     */
    public function index()
    {
        return view('admin.subscription.index');
    }

    /**
     * Display the form to add a new subscription.
     *
     * @return \Illuminate\View\View The view for adding a new subscription.
     */
    public function addSubscriptionForm()
    {
        return view('admin.subscription.add');
    }

    /**
     * Retrieve subscription data with optional filtering, search, and sorting.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing filters and sorting options.
     * @return \Illuminate\Http\JsonResponse A JSON response containing subscription data, total records, and pagination details.
     */
    public function getSubscriptionData(Request $request)
    {
        $query = Subscription::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter if present
        if ($request->has('status_filter') && $request->status_filter != '') {
            $status = $request->status_filter;
            $query->where('status', $status);
        }

        // Handle sorting
        $orderColumnIndex = $request->input('order.0.column');  // Get the column index to order by
        $orderDirection = $request->input('order.0.dir');  // Get the order direction (asc/desc)
        $columns = $request->input('columns');  // Columns data (from DataTable)

        // Map column index to actual column name (based on your DataTable definition)
        $orderColumn = $columns[$orderColumnIndex]['name'];

        // Apply sorting to query
        $query->orderBy($orderColumn, $orderDirection);

        // Pagination (server-side)
        $totalRecords = $query->count();
        $subscriptions = $query->skip($request->start)
                               ->take($request->length)
                               ->get();

        // Return DataTables format response
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,  // Adjust if filtering is applied
            'data' => $subscriptions,
        ]);
    }

    /**
     * Handle the addition of a new subscription.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the subscription data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating success or failure.
     */
    public function addSubscription(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'name' => 'required|unique:subscriptions,name|max:255',
        ]);

        // Call the subscription service to store the subscription
        $response = $this->subscription_service->storeSubscription($request);

        // If the subscription was successfully added, redirect to the subscription list
        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('subscription-list');
        }

        // If there was an error, redirect back with an error message
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Handle the updation of a  subscription.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the subscription data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating success or failure.
     */
   public function updateSubscription(Request $request, $id){
        // Validate the incoming request data
        $request->validate([
            'name' => 'required',
            'android_sku_code' => 'nullable|string',
            'ios_sku_code' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        // Call the subscription service to update the subscription
        $response = $this->subscription_service->updateSubscription($request, $id);

        // If the subscription was successfully updated, redirect to the subscription list
        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('subscription-list');
        }

        // If there was an error, redirect back with an error message
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }


    /**
     * Update the status of a subscription.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the subscription ID and new status.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function updateSubscriptionStatus(Request $request)
    {
        // Call the subscription service to update the subscription status
        $response = $this->subscription_service->subscriptionStatus($request);

        // Handle the response from the service
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Subscription status changed successfully.',
            ]);
        }

        // If there was an error, return a failure response
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }

    /**
     * Delete a subscription from the database.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance, which holds session data.
     * @param int $subscription_id The ID of the subscription to delete.
     * @return \Illuminate\Http\RedirectResponse A redirect response to either the subscription list or the previous page with a success or error message.
     */
    public function deleteSubscription(Request $request, $subscription_id)
    {
        $subscription = Subscription::find($subscription_id);

        if ($subscription) {
            // If the subscription is found, delete it
            $subscription->delete();

            // Flash a success message to the session and redirect to the subscription list
            $request->session()->flash('success', 'Subscription deleted successfully');
            return redirect()->route('subscription-list');
        }

        // If the subscription does not exist, flash an error message to the session
        $request->session()->flash('error', 'Subscription does not exist');
        return redirect()->back();
    }

    /**
     * Display the details of a specific subscription.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $subscription_id The ID of the ticket.
     * @return \Illuminate\View\View The view displaying the subscription details.
     */
    public function SubscriptionDetails(Request $request, $subscription_id)
    {
        $subscription_detail = Subscription::find($subscription_id);
       // return $subscription_detail;
        return view('admin.subscription.view', compact('subscription_detail'));
    }

}
