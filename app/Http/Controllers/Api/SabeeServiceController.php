<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Hotel, HotelRoomType, HotelRatePlan};
use App\Traits\SendResponseTrait;
use App\Services\API\SabeeServiceListService;  
use Carbon\Carbon;
use Illuminate\Support\Facades\{Auth, Hash, Validator};

class SabeeServiceController extends Controller
{
    use SendResponseTrait;

    protected $serviceList;

    public function __construct(SabeeServiceListService $serviceList)
    {
        $this->serviceList = $serviceList;
    }

    /**
     * functionName : fetchAndStore
     * createdDate  : 2025-04-23
     * purpose      : Get hotel services from SabeeApp and save in our db
     */
    public function fetchAndStore(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|integer|exists:hotels,hotel_id',
        ]);

        $hotelId = $request->input('hotel_id');

        try {
            // This will fetch & upsert all services for the given hotel
            $services = $this->serviceList->fetchAndSyncServiceInventory($hotelId);

            return $this->apiResponse(
                'success',
                200,
                'Service inventory synced successfully.',
                ['services' => $services]
            );
        } catch (\Exception $e) {
            return $this->apiResponse(
                'error',
                400,
                'Failed to sync services: ' . $e->getMessage()
            );
        }
    }

    public function submitService(Request $request)
    {
        $request->validate([
            'hotel_id' => 'required|integer',
            'reference_id' => 'required|string',
            'reservation_code' => 'required|string',
            'services' => 'required|array',
            'services.*.service_id' => 'required|integer',
            'services.*.name' => 'required|string',
            'services.*.quantities' => 'nullable|array',
            'services.*.quantities.*.value' => 'required_with:services.*.quantities|integer',
            'services.*.quantities.*.date' => 'required_with:services.*.quantities|date',
        ]);

        try {
            $payload = $request->only(['hotel_id', 'reference_id', 'reservation_code', 'services']);
            $response = $this->serviceList->submitService($payload);

            return response()->json([
                'status' => 'success',
                'message' => 'Service submitted successfully',
                'data' => $response
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
