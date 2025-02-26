<?php

namespace App\Classes\Carriers;

use Illuminate\Support\Facades\Http;

class PostNL implements Carrier
{
    private $name;
    private $url = 'https://api.postnl.nl';

    public function __construct()
    {
        $this->name = 'PostNL';
    }

    public function getName()
    {
        return $this->name;
    }
    public function authenticate()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'ApiKey' => config('carriers.postnl'),
        ]);
    }

    public function locations()
    {
        $response = $this->authenticate()->get($this->url . '/shipment/v2_1/locations/nearest', [
            'Latitude' => config('app.default_lat'),
            'Longitude' => config('app.default_lng')
        ])->json();

        return collect($response['GetLocationsResult']['ResponseLocation'])->map(fn($item) => ['lat' => $item['Latitude'], 'long' => $item['Longitude']])->toArray();
    }
}
