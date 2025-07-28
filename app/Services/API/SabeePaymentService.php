<?php

namespace App\Services\API;

use App\Models\{Hotel, HotelRoomType, HotelRatePlan, HotelRatePlanRoomType};
use App\Models\Booking;
use App\Models\BookingCustomer;
use App\Models\BookingGuest;
use App\Models\BookingPrice;
use App\Models\BookingService;
use App\Models\BookingServicePrice;
use App\Models\BookingPayment;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class SabeePaymentService
{
    /**
     * Fetch Payment for booking from Sabee API.
     *
     * @return array
     * @throws \Exception
     */
   public function fetchPaymentChanges($hotelId, $paymentDateTime){
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . '/payment/changes', [
            'hotel_id' => $hotelId,
            'payment_date_time' => $paymentDateTime,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch payment changes: ' . $response->body());
        }

        return $response->json(); // Return full response (or adjust if specific key needed)
    }


    /**
     * Fetch hotel inventory and save/update hotels, room types, and rate plans in DB.
     *
     * @return array
     * @throws \Exception
     */
         public function fetchAndStorePayments($hotelId, $paymentDateTime = '2024-07-06 13:46:26')
        {
            $response = Http::withHeaders([
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
            ])->get(config('services.sabee.api_url') . '/payment/changes', [
                'hotel_id' => $hotelId,
                'payment_date_time' => $paymentDateTime,
            ]);

            if (!$response->successful()) {
                throw new \Exception('Failed to fetch payment changes: ' . $response->body());
            }

            $reservationCodes = $response->json('data.reservation_codes') ?? [];

            foreach ($reservationCodes as $code) {
                \App\Models\SabeePaymentChange::updateOrCreate(
                    ['reservation_code' => $code],
                    [
                        'hotel_id' => $hotelId,
                        'changed_at' => now()
                    ]
                );
            }

            return $reservationCodes;
        }


    /**
     * Fetch room types by hotel ID from Sabee API.
     *
     * @param int|string $hotelId
     * @return array
     * @throws \Exception
     */
    public function fetchRoomTypesByHotelId($hotelId)
    {
        // Send GET request to fetch room types
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . "/roomtype/list", [
            'hotel_id' => $hotelId
        ]);

        // Throw exception on failure
        if (!$response->successful()) {
            throw new \Exception('Failed to fetch room types: ' . $response->body());
        }

        // Return room types array
        return $response->json('data.room_types');
    }

    /**
     * Get hotel details from local database with relations (room types and rate plans).
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function hotelDetail($id)
    {
        $hotel = Hotel::with('roomTypes', 'ratePlans','hotelImages', 'feedbacks','categories','categories.subCategories')->where('hotel_id', $id)->first();

        if ($hotel) {
            return response()->json([
                'status' => 'success',
                'message' => 'Hotel details fetched successfully.',
                'data' => $hotel
            ], 200);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Hotel not found.',
            ], 404);
        }
    }



    /**
     * Submit payment info to Sabee API.
     *
     * @param int|string $hotelId
     * @param string $referenceId
     * @param string $reservationCode
     * @param array $payments
     * @return array|bool
     */
    public function submitPaymentToSabee($hotelId, $reservationCode, array $payments)
    {
        $payload = [
            'hotel_id' => $hotelId,
            'reference_id' =>uniqid(),
            'reservation_code' => $reservationCode,
            'payments' => $payments,
        ];

        try {
            $response = Http::withHeaders([
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
                'Content-Type' => 'application/json',
            ])->post(config('services.sabee.api_url') . 'payment/submit', $payload);

            if ($response->successful()) {
                return $response->json();
            }
           $booking = Booking::where('reservation_code', $reservationCode)->first();

            BookingPayment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_status' => 'complete',
                    'payment_type'   => $payments['payment_method'] ?? null,
                ]
            );

            Log::error('SabeeApp payment submit failed: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('SabeeApp payment submit exception: ' . $e->getMessage());
            return false;
        }
    }
}
