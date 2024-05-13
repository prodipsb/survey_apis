<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getLocationName($latitude, $longitude)
    {
        

        if(config('services.google_map.google_map_activation')){

            $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'latlng' => $latitude.','.$longitude,
                    'key' => config('services.google_map.google_map_api_key'),
                ],
            ]);
    
            $data = json_decode($response->getBody(), true);
    
            if (!empty($data['results'])) {
                $dataArray = $data['results'];
                $formattedAddresses = $this->extractFormattedAddressesFromObject($dataArray);
                $formattedAddresses['display_name'] = $formattedAddresses[5];
                return $formattedAddresses;
    
            }

        }else{
            $response = Http::get("https://nominatim.openstreetmap.org/reverse?format=json&lat=$latitude&lon=$longitude");

            // Get the JSON response body
            return $response->json();
    
        }
        
        
        return null;
    }



    function extractFormattedAddressesFromObject($data) {
        $addresses = [];
    
        foreach ($data as $item) {
            if (isset($item['formatted_address'])) {
                $addresses[] = $item['formatted_address'];
            }
        }
    
        return $addresses;
    }

    

}
