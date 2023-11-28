<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Device; // Adjust the namespace based on your model location
use Illuminate\Support\Facades\Http;

class RemoveInvalidDeviceTokens extends Command
{
    protected $signature = 'tokens:remove-invalid';
    protected $description = 'Remove invalid device tokens from the database';

    public function handle()
    {
        $tokens = Device::pluck('device_token');

        foreach ($tokens as $token) {
            if (!$this->isValidToken($token)) {
                $this->removeInvalidToken($token);
            }
        }

        $this->info('Invalid tokens removed successfully.');
    }

    private function isValidToken($token)
    {
        $response = Http::post('https://fcm.googleapis.com/fcm/send', [
            'to' => $token,
            'data' => ['test' => 'validation'], // Adjust the payload based on your needs
        ], [
            'Authorization' => 'key=AAAAsRTlG0s:APA91bGi2Ez89m460zFWYUg02Y7cXgsFqvluQIqPkRnRdfj2Z6_Qz-Ex9JtYZoxVsfia-zcHCWwe6ObOVfEKjrA_1Udg9s6_2FUQF-iWhXgedTKDn8HNO3G_6pr9GGRb2-aa9KjZRkbF',
            'Content-Type' => 'application/json',
        ]);

        return $response->successful();
    }

    private function removeInvalidToken($token)
    {
        Device::where('device_token', $token)->delete();
        $this->info("Invalid token $token removed from the database.");
    }
}
