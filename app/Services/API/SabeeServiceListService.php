<?php

namespace App\Services\API;

use Illuminate\Support\Facades\Http;
use App\Models\Service;

class SabeeServiceListService
{
    /**
     * Fetch the service inventory from SabeeApp and sync to local database.
     *
     * @param  int  $hotelId
     * @return array<int, array>
     * @throws \Exception
     */
    public function fetchAndSyncServiceInventory(int $hotelId): array
    {
        $response = Http::withHeaders([
            'api_key'     => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . "service/list?hotel_id={$hotelId}");

        if (! $response->successful()) {
            throw new \Exception('Failed to fetch service inventory: ' . $response->body());
        }

        $services = $response->json('data.services');
        if ($services) {
            foreach ($services as $serviceData) {
                // Upsert service in local database
                Service::updateOrCreate(
                    [
                        'service_id' => $serviceData['service_id'],
                        'hotel_id'   => $hotelId,
                    ],
                    [
                        'service_name'           => $serviceData['service_name'],
                        'service_category_name'  => $serviceData['service_category_name'],
                        'description'            => $serviceData['description'],
                        'image_url'              => $serviceData['image_url'],
                        'included'               => (bool) $serviceData['included'],
                        'compulsory'             => (bool) $serviceData['compulsory'],
                        'price_type'             => $serviceData['price_type'],
                        'price_applicable'       => $serviceData['price_applicable'],
                        'billing_type'           => $serviceData['billing_type'],
                        'unit'                   => $serviceData['unit'],
                        'price'                  => $serviceData['price'],
                        'vat'                    => $serviceData['vat'],
                        'apply_city_tax'         => (bool) $serviceData['apply_city_tax'],
                        'currency'               => $serviceData['currency'],
                        'available_rateplans'    => $serviceData['available_rateplans'],
                    ]
                );
            }
            return $services;
        }else{
            return [];
        }


        
    }
}
