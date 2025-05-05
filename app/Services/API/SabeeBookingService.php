<?php

namespace App\Services\API;

use App\Models\Booking;
use App\Models\BookingCustomer;
use App\Models\BookingGuest;
use App\Models\BookingPrice;
use App\Models\BookingService;
use App\Models\BookingServicePrice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\Service;
use Illuminate\Support\Facades\Log;


class SabeeBookingService
{
    /**
     * Fetch the booking inventory from SabeeApp and sync to local database.
     *
     * @param  int  $hotel_id
     * @param  string  $start_date
     * @param  string  $end_date
     * @param  int  $extended_list
     * @param  int  $services
     * @param  int  $guest_details
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

        $reservations = $response->json('data.reservations');


        if (!is_array($reservations) || empty($reservations)) {
            throw new \Exception('No reservations found in the response.');
        }

        foreach ($reservations as $reservation) {
            // Create or update the booking
            $bookingModel = Booking::updateOrCreate(
                ['reservation_code' => $reservation['reservation_code']],
                [
                    'hotel_id' => $hotel_id,
                    'status' => $reservation['status'],
                    'room_type_id' => $reservation['room_type_id'],
                    'room_type_name' => $reservation['room_type_name'],
                    'room_id' => $reservation['room_id'],
                    'room_name' => $reservation['room_name'],
                    'guest_count' => $reservation['guest_count'],
                    'rateplan' => $reservation['rateplan_applied'],
                    'license_plate' => $reservation['license_plate'],
                    'channel_id' => $reservation['channel_id'],
                    'door_code' => $reservation['random_generated_door_code'],
                    'number_of_guests' => $reservation['number_of_guests'],
                    'price' => $reservation['rooom_price'] ?? 0,
                    'paid' => $reservation['paid'],
                    'currency' => $reservation['currency'],
                    'checkin_date' => $reservation['checkin_date'],
                    'checkout_date' => $reservation['checkout_date'],
                    'comment' => $reservation['comment'],
                    'created_at_api	' => $reservation['create_date_time'],
                    'updated_at_api' => $reservation['modified_date_time'],
                ]
            );

            // Save customer details
            if (!empty($reservation['customer'])) {
                $customer = $reservation['customer'];
                BookingCustomer::updateOrCreate(
                    ['booking_id' => $bookingModel->id],
                    [
                        'email' => $customer['email'],
                        'first_name' => $customer['first_name'],
                        'last_name' => $customer['last_name'],
                        'birth_date' => $customer['birth_date'],
                        'citizenship' => $customer['citizenship'],
                        'address' => $customer['address'],
                        'city' => $customer['city'],
                        'zip' => $customer['zip'],
                        'country_code' => $customer['country_code'],
                        'phone_number' => $customer['phone_number'],
                        'remarks' => $customer['remarks'],
                    ]
                );
            }

            // Save guests
            if (!empty($reservation['guests']) && is_array($reservation['guests'])) {
                foreach ($reservation['guests'] as $guest) {
                    BookingGuest::updateOrCreate(
                        [
                            'booking_id' => $bookingModel->id,
                            'email' => $guest['email'] ?? null,
                        ],
                        [
                            'first_name' => $guest['first_name'] ?? null,
                            'last_name' => $guest['last_name'] ?? null,
                            'birth_date' => $guest['birth_date'] ?? null,
                            'citizenship' => $guest['citizenship'] ?? null,
                            'address' => $guest['address'] ?? null,
                            'phone_number' => $guest['phone_number'] ?? null,
                            'remarks' => $guest['remarks'] ?? null,
                        ]
                    );
                }
            }

            // Save prices
            if (!empty($reservation['prices']) && is_array($reservation['prices'])) {
                foreach ($reservation['prices'] as $price) {
                    BookingPrice::updateOrCreate(
                        [
                            'booking_id' => $bookingModel->id,
                            'date' => $price['date'],
                        ],
                        [
                            'vat' => $price['vat'] ?? 0,
                            'city_tax' => $price['city_tax'] ?? 0,
                            'amount' => $price['amount'] ?? 0,
                        ]
                    );
                }
            }

            if (!empty($reservation['services']) && is_array($reservation['services'])) {
                foreach ($reservation['services'] as $service) {

                    $bookingService = BookingService::updateOrCreate(
                        [
                            'booking_id' => $bookingModel->id,
                            'service_id' => $service['service_id'] ?? 0,
                        ],
                        [
                            'service_name' => $service['service_name'],
                            'description' => $service['description'] ?? null,
                            'total_price' => $service['total_price'] ?? 0,
                        ]
                    );

                    // Store service prices
                    if (!empty($service['prices']) && is_array($service['prices'])) {
                        foreach ($service['prices'] as $price) {
                            BookingServicePrice::updateOrCreate(
                                [
                                    'booking_service_id' => $bookingService->id,
                                    'date' => $price['date'],
                                ],
                                [
                                    'quantity' => $price['quantity'] ?? 1,
                                    'vat' => $price['vat'] ?? 0,
                                    'city_tax' => $price['city_tax'] ?? 0,
                                    'amount' => $price['amount'] ?? 0,
                                ]
                            );
                        }
                    }
                }
            } else {
                // Handle case where no services are available
                Log::info('No services found for reservation', [
                    'reservation_code' => $reservation['reservation_code'],
                    'guest_count' => $reservation['guest_count'],
                    'room_type' => $reservation['room_type_name']
                ]);
            }
        }

        return $reservations;
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
        try {
            $response = Http::withHeaders([
                'api_key' => config('services.sabee.api_key'),
                'api_version' => config('services.sabee.api_version'),
                'Content-Type' => 'application/json',
            ])->post(config('services.sabee.api_url') . '/booking/submit', $payload);

            if (!$response->successful()) {
                throw new \Exception('Failed to create booking: ' . $response->body());
            }

            // Get the reservation code from the response
            $responseData = $response->json('data');
            $reservationCode = $responseData['reservation_code'] ?? null;

            if (!$reservationCode) {
                throw new \Exception('Reservation code not found in response.');
            }

            // Fetch bookings for today or the relevant date range
            $today = now()->toDateString();
            $bookings = $this->fetchBookings($payload['hotel_id'], $today, $today);

            // Find the created booking from the list of fetched bookings
            foreach ($bookings as $booking) {
                if ($booking['reservation_code'] === $reservationCode) {
                    // Call the method to save the detailed booking data
                    $this->saveBookingData($booking);
                    return $booking;
                }
            }

            throw new \Exception("Created booking with reservation code '{$reservationCode}' not found in today's bookings.");
        } catch (\Exception $e) {
            \Log::error('Sabee createBooking error: ' . $e->getMessage());
            throw $e; // Or return ['error' => true, 'message' => $e->getMessage()] if you prefer
        }
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
