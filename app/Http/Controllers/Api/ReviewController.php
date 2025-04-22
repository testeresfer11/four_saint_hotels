<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use App\Traits\SendResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash,Validator};

class ReviewController extends Controller
{
    use SendResponseTrait;

    public function index($id)
    {
        try {
            $feedbacks = Feedback::where('hotel_id', $id)->latest()->get();
            $averageRating = Feedback::where('hotel_id', $id)->avg('rating');

            return $this->apiResponse('success',200,'Feedback ' . config('constants.SUCCESS.FETCH_DONE'),['average_rating' => round($averageRating, 2),'feedbacks' => $feedbacks]);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }


    public function getUserFeedback()
    {
        try {
            $feedbacks = Feedback::where('user_id', Auth::id())->latest()->get();

            return $this->apiResponse('success',200,'Feedback ' . config('constants.SUCCESS.FETCH_DONE'),$feedbacks);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }






    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:1000',
                'rating' => 'nullable|integer|min:1|max:5'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $feedback = Feedback::create([
                'user_id' => authId(),
                'hotel_id' => $request->hotel_id,
                'message' => $request->message,
                'rating' => $request->rating,
            ]);

            return $this->apiResponse('success', 200, 'Feedback ' . config('constants.SUCCESS.ADD_DONE'), $feedback);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $feedback = Feedback::find($id);

            if (!$feedback) {
                return $this->apiResponse('error', 404, 'Feedback not found');
            }

            $validator = Validator::make($request->all(), [
                'message' => 'sometimes|required|string|max:1000',
                'rating'  => 'sometimes|nullable|integer|min:1|max:5'
            ]);

            if ($validator->fails()) {
                return $this->apiResponse('error', 422, $validator->errors()->first());
            }

            $feedback->update($request->only(['message', 'rating']));

            return $this->apiResponse('success', 200, 'Feedback ' . config('constants.SUCCESS.UPDATE_DONE'), $feedback);
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }


    public function destroy($id)
    {
        try {
            $feedback = Feedback::find($id);

            if (!$feedback) {
                return $this->apiResponse('error', 404, 'Feedback not found');
            }

            // Optional: Ensure the authenticated user owns this feedback
            if ($feedback->user_id !== authId()) {
                return $this->apiResponse('error', 403, 'You are not authorized to delete this feedback');
            }

            $feedback->delete();

            return $this->apiResponse('success', 200, 'Feedback ' . config('constants.SUCCESS.DELETE_DONE'));
        } catch (\Exception $e) {
            return $this->apiResponse('error', 400, $e->getMessage());
        }
    }
}
