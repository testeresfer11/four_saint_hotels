<?php 
namespace App\Services\API;

use Spatie\Permission\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Http;


class SabeeHotelService
{
    public function fetchHotelInventory(){
    $response = Http::withHeaders([
            'api_key' => config('services.sabee.api_key'),
            'api_version' => config('services.sabee.api_version'),
        ])->get(config('services.sabee.api_url') . '/hotel/inventory');

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch hotel inventory: ' . $response->body());
        }

        return $response->json('data.hotels');
    }

}
?>