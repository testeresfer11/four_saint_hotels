<?php 

namespace App\Services\API;

use App\Models\Page;
use App\Models\PageContent;
use App\Models\Slider;

class PagesService
{
    public function getPageDetails($request)
    {
        try{

            $slug = $request->slug;

            // Check if the slug is provided
            if (!$slug) {
                return [
                    'success' => false,
                    'message' => 'Slug is required.',
                ];
            }

            // Reusable filter closure for filtering page translations
            // $filter = function ($query) use ($country_code, $lang_code) {
            //     if ($country_code) {
            //         $query->where('country_code', $country_code);
            //     }
            //     if ($lang_code) {
            //         $query->where('lang_code', $lang_code);
            //     }
            // };

            // Retrieve the page with filtered content
            $page = Page::where('slug', $slug)
                ->with(['pageContent'])
                ->first();

            // Check if the page exists
            if (!$page) {
                return [
                    'success' => false,
                    'message' => 'Page not found.',
                ];
            }
            
            // Return success response with page data
            return [
                'success' => true,
                'message' => 'Page retrieved successfully.',
                'data' => $page,
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
