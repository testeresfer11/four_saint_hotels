<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function getList(Request $request)
    {
        $hotelId = session('selected_hotel_id', 8618);

        $paymentDateTime = '2024-07-06 13:46:26';


        if (!$hotelId || !$paymentDateTime) {
            return response()->json([
                'status' => 'error',
                'message' => 'hotel_id and payment_date_time are required'
            ], 422);
        }

        $url = 'https://api.sabeeapp.com/connect/payment/changes';

        try {
            $response = Http::withHeaders([
                'api_key' => 'febfaf24b51e25e5f7a4e0d0f8ca01a5',
                'api_version' => '1',
            ])->get($url, [
                'hotel_id' => $hotelId,
                'payment_date_time' => $paymentDateTime,
            ]);

            if ($response->successful()) {
                return response()->json([
                    'status' => 'success',
                    'data' => $response->json(),
                ]);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to fetch payment data',
                    'details' => $response->body(),
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Exception occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
