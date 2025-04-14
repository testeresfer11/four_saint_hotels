<?php 

namespace App\Services\Admin;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;
use App\Models\Faq;

class FaqService
{
    /**
     * Store a new subscription in the database.
     *
     * @param \Illuminate\Http\Request $request The request instance containing subscription details.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function storeFaq($request)
    {
        try {
            // Create a new subscription record in the database
            $faq = Faq::create([
                'question' => $request->question,
                'answer' => $request->answer
            ]);

            // Return success response with the created subscription data
            return [
                'success' => true,
                'message' => 'Faq added successfully',
                'data' => $faq
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
     * Update the status of an existing faq.
     *
     * @param \Illuminate\Http\Request $request The request instance containing the faq ID.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function faqStatus($request)
    {
        try {
            // Retrieve the faq using the faq ID from the request
            $faq_id = $request->faq_id;
            $faq = Faq::find($faq_id);
        
            if (!$faq) {
                // If the faq is not found, return an error message
                return [
                    'success' => false,
                    'message' => 'Faq not found.',
                ];
            }

            // Toggle the faq status (active -> inactive or inactive -> active)
            $status = $faq->status;
            $status = ($status == true) ? 0 : 1;

            // Update the faq's status in the database
            $faq->update(['status' => $status]);

            // Return a success response after updating the status
            return [
                'success' => true,
                'message' => 'Faq status changed successfully.',
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
     * Upadte a  faq in the database.
     *
     * @param \Illuminate\Http\Request $request The request instance containing faq details.
     * @return array Response array indicating success or failure with an appropriate message.
     */
    public function updateFaq($request, $id){
        try {
            // Find the faq by ID
            $faq = Faq::findOrFail($id);
            
            // Update the subscription with the new data
            $faq->update([
                 'question' => $request->question,
                'answer' => $request->answer
            ]);
            
            // Return success response with the updated subscription data
            return [
                'success' => true,
                'message' => 'Faq updated successfully',
                'data' => $faq
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
