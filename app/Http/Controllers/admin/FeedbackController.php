<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;


class FeedbackController extends Controller
{
    /**
     * functionName : index
     * createdDate  : 22-04-2025
     * purpose      : list of feedbacks
     */
public function index(Request $request)
{
    $feedbacks = Feedback::with(['user'])
        ->when($request->filled('status'), function ($query) use ($request) {
            $query->where('status', $request->status);
        })
        ->when($request->filled('search_keyword'), function ($query) use ($request) {
            $keyword = $request->search_keyword;

            $query->where(function ($q) use ($keyword) {
                $q->where('message', 'like', '%' . $keyword . '%')
                  ->orWhereHas('user', function ($q2) use ($keyword) {
                      $q2->orWhere('full_name', 'like', '%' . $keyword . '%');
                  });
            });
        })
        ->orderBy('created_at', 'desc')
        ->paginate(10);

    return view('admin.feedback.list', compact('feedbacks'));
}



    /**End method index**/

    /**
     * functionName : show
     * createdDate  : 22-04-2025
     * purpose      : show the feedback detail
     */
    public function show($id)
    {
        $feedback = Feedback::with('user')->findOrFail($id);
        return view('admin.feedback.view', compact('feedback'));
    }

    /**End method show**/


    /**
     * functionName : delete
     * createdDate  : 22-04-2025
     * purpose      : Delete the feedback  by id
     */
    public function delete($id)
    {
        try {
            // Find the language or fail with 404
            $language = Feedback::findOrFail($id);

            // Delete the language
            $language->delete();

            return response()->json([
                "status" => "success",
                "message" => "Announcement " . config('constants.SUCCESS.DELETE_DONE')
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "status" => "error",
                "message" => "Announcement not found"
            ], 404);
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Feedback deletion error: ' . $e->getMessage());

            return response()->json([
                "status" => "error",
                "message" => "An error occurred while deleting the Announcement"
            ], 500);
        }
    }
    /**End method delete**/

    /**
     * functionName : changeStatus
     * createdDate  : 22-04-2025
     * purpose      : Update the feedback status 
     */
    public function changeStatus(Request $request)
    {
        try {

            $feedback = Feedback::findOrFail($request->id); 
              
            $feedback->status = $request->status; 
            $feedback->save();
            return response()->json(["status" => "success", "message" => "Feedback status " . config('constants.SUCCESS.CHANGED_DONE')], 200);
        } catch (\Exception $e) {
            return response()->json(["status" => "error", $e->getMessage()], 500);
        }
    }
    /**End method changeStatus**/
}
