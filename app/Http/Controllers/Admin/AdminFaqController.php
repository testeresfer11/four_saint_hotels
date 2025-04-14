<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Faq;

class AdminFaqController extends AdminBaseController
{
    /**
     * Display the faq index page.
     *
     * @return \Illuminate\View\View The view for the faq index page.
     */
    public function index()
    {
        return view('admin.faq.index');
    }

    /**
     * Display the form to add a new  faq.
     *
     * @return \Illuminate\View\View The view for adding a new faq.
     */
    public function addFaqForm()
    {
        return view('admin.faq.add');
    }

    /**
     * Retrieve faq data with optional filtering, search, and sorting.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing filters and sorting options.
     * @return \Illuminate\Http\JsonResponse A JSON response containing faq data, total records, and pagination details.
     */
    public function getFaqData(Request $request)
    {
        $query = Faq::query();

        // Apply custom search if present
        if ($request->has('custom_search') && $request->custom_search) {
            $searchTerm = $request->custom_search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('question', 'like', "%{$searchTerm}%")
                  ->orWhere('answer', 'like', "%{$searchTerm}%");
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
        $faq = $query->skip($request->start)
                               ->take($request->length)
                               ->get();

        // Return DataTables format response
        return response()->json([
            'draw' => $request->draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalRecords,  // Adjust if filtering is applied
            'data' => $faq,
        ]);
    }

    /**
     * Handle the addition of a new Faq.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the faq data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating success or failure.
     */
    public function addFaq(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'question' => 'required',
            'answer' => 'required',
        ]);

        // Call the faq service to store the faq
        $response = $this->faq_service->storeFaq($request);

        // If the faq was successfully added, redirect to the faq list
        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('faq-list');
        }

        // If there was an error, redirect back with an error message
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }

    /**
     * Handle the updation of a  faq.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the faq data.
     * @return \Illuminate\Http\RedirectResponse A redirect response indicating success or failure.
     */
    public function updateFaq(Request $request, $id){

        $response = $this->faq_service->updateFaq($request, $id);

        // If the faq was successfully updated, redirect to the faq list
        if ($response['success']) {
            $request->session()->flash('success', $response['message']);
            return redirect()->route('faq-list');
        }

        // If there was an error, redirect back with an error message
        $request->session()->flash('error', $response['message']);
        return redirect()->back();
    }


    /**
     * Update the status of a faq.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance containing the faq ID and new status.
     * @return \Illuminate\Http\JsonResponse A JSON response indicating the success or failure of the operation.
     */
    public function updateFaqStatus(Request $request)
    {
        // Call the faq service to update the faq status
        $response = $this->faq_service->faqStatus($request);

        // Handle the response from the service
        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Faq status changed successfully.',
            ]);
        }

        // If there was an error, return a failure response
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong. Please check and try again.',
        ]);
    }

    /**
     * Delete a faq from the database.
     *
     * @param \Illuminate\Http\Request $request The HTTP request instance, which holds session data.
     * @param int $faq_id The ID of the faq to delete.
     * @return \Illuminate\Http\RedirectResponse A redirect response to either the faq list or the previous page with a success or error message.
     */
    public function deleteFaq(Request $request, $faq_id)
    {
        $faq = Faq::find($faq_id);

        if ($faq) {
            // If the faq is found, delete it
            $faq->delete();

            // Flash a success message to the session and redirect to the faq list
            $request->session()->flash('success', 'Faq deleted successfully');
            return redirect()->route('faq-list');
        }

        // If the faq does not exist, flash an error message to the session
        $request->session()->flash('error', 'Faq does not exist');
        return redirect()->back();
    }

    /**
     * Display the details of a specific faq.
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param int $faq_id The ID of the ticket.
     * @return \Illuminate\View\View The view displaying the subscription details.
     */
    public function FaqDetails(Request $request, $faq_id)
    {
        $faq_detail = Faq::find($faq_id);
        return view('admin.faq.view', compact('faq_detail'));
    }

}
