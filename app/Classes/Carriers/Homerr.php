<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Homerr implements Carrier
{
    private $name;
    private $url = 'https://homerr-functions-production.azurewebsites.net/api';

    public function __construct()
    {
        $this->name = 'Homerr';
    }

    public function getName()
    {
        return $this->name;
    }

    public function authenticate()
    {
        // TODO: Implement authenticate() method.
    }

    public function locations(array $data)
    {
        $locations = Http::get($this->url . '/v1/homerrs/dropoff', [
            'postalcode' => $data['postal'],
            'number' => $data['number'],
            'country' => $data['country'],
            'limit' => $data['limit'],
        ])->json();

        $mappedLocations = [];

        foreach ($locations as $location) {
            $mapped = [
                'external_id' => $location['id'],
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location['street'],
                'number' => trim($location['houseNumber'] . ' ' . $location['houseAddition'] . ' ' . $location['houseLetter']),
                'postal_code' => $location['postalCode'],
                'city' => $location['city'],
                'country' => $location['country'],
                'telephone' => null,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
            ];

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
