<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use App\Models\Service;

class SabeeBookingService
{
    /**
     * Fetch the booking inventory from SabeeApp and sync to local database.
     *
     * @param  int  $hotelId
     * @return array<int, array>
     * @throws \Exception
     */
    public function fetchBookings($hotel_id, $start_date, $end_date, $extended_list = 1, $services = 1, $guest_details = 1)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . '/booking/list', [
            'hotel_id' => $hotel_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'extended_list' => $extended_list,
            'services' => $services,
            'guest_details' => $guest_details,
        ]);
        

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch bookings: ' . $response->body());
        }

        return $response->json('data');
    }


    /**
     * Create the booking in SabeeApp and sync to local database.
     *
     * @param  int  $hotelId
     * @return array<int, array>
     * @throws \Exception
     */
    public function createBooking($payload)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
            'Content-Type' => 'application/json',
        ])->post(config('services.sabee.api_url') . '/booking/submit', $payload);
        if (!$response->successful()) {
            throw new \Exception('Failed to create booking: ' . $response->body());
        }


        return $response->json();
    }


      /**
     * update the booking in SabeeApp and sync to local database.
     *
     * @param  int  $hotelId
     * @return array<int, array>
     * @throws \Exception
     */

    public function updateBooking($payload)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
            'Content-Type' => 'application/json',
        ])->post(config('services.sabee.api_url') . '/booking/modify', $payload);
        if (!$response->successful()) {
            throw new \Exception('Failed to create booking: ' . $response->body());
        }

        return $response->json();
    }

     /**
     * cancel the booking in SabeeApp and sync to local database.
     *
     * @param  int  $hotelId
     * @return array<int, array>
     * @throws \Exception
     */


    public function cancelBooking($payload)
    {
        $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
            'Content-Type' => 'application/json',
        ])->post(config('services.sabee.api_url') . '/booking/cancel', $payload);
        if (!$response->successful()) {
            throw new \Exception('Failed to create booking: ' . $response->body());
        }

        return $response->json();
    }




}
