<?php 
namespace App\Services\API;

use Illuminate\Support\Facades\Auth;

// Models
use App\Models\User;
use App\Models\Faq;

class FaqService
{
    /**
     * Retrieve a list of active Faq.
     *
     * This method fetches all Faq that are currently active (status = true),
     * ordered by their ID in descending order.
     *
     * @param \Illuminate\Http\Request $request
     * @return array Response indicating the success or failure of the operation, 
     *                along with the list of Faq or an error message.
     */
    public function getFaqList($request)
    {
        try {
            // Fetch active faq ordered by ID in descending order
            $faq = Faq::where('status', true)
                                         ->orderBy('id', 'desc')
                                         ->get();

            // If faq are found, return success with the data
            if ($faq->isNotEmpty()) {
                return [
                    'success' => true,
                    'message' => 'Faq retrieved successfully',
                    'data' => $faq
                ];
            }

            // If no subscriptions exist, return a failure message
            return [
                'success' => false,
                'message' => 'Faq do not exist',
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
