<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        $locations = $this->authenticate()->get($this->url . '/shipment/v2_1/locations/nearest', [
            'Latitude' => config('app.default_lat'),
            'Longitude' => config('app.default_lng')
        ])->json();

        $mappedLocations = [];

        foreach ($locations['GetLocationsResult']['ResponseLocation'] as $location) {
            $mapped = [
                'external_id' => $location['LocationCode'],
                'name' => $location['Name'],
                'slug' => Str::slug($location['Name']),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location['Address']['Street'],
                'number' => $location['Address']['HouseNr'] . trim(' ' . ($location['Address']['HouseNrExt'] ?? null)),
                'postal_code' => $location['Address']['Zipcode'],
                'city' => $location['Address']['City'],
                'country' => $location['Address']['Countrycode'],
                'telephone' => null,
                'latitude' => $location['Latitude'],
                'longitude' => $location['Longitude'],
            ];

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
