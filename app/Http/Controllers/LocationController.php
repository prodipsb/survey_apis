<?php

namespace App\Http\Controllers;

use App\Services\GeocodingService;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    protected $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }

    public function getLocationName(Request $request)
    {
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        $locationName = $this->geocodingService->getLocationName($latitude, $longitude);

        return response()->json(['location_name' => $locationName]);
    }
}
