<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Intrapost implements Carrier
{
    private $name;
    private $url = 'https://api.intrapost.nl';

    public function __construct()
    {
        $this->name = 'Intrapost';
    }

    public function getName()
    {
        return $this->name;
    }

    public function authenticate()
    {
        return [
            'ApiKey' => config('carriers.intrapost.apiKey'),
            'AccountNumber' => config('carriers.intrapost.accountNumber'),
        ];
    }

    public function locations(array $data)
    {
        $parameters = [
            'Zipcode' => $data['postal'],
            'Number' => $data['number'],
            'Limit' => $data['limit'],
            'CountryCode' => $data['country']
        ];

        $locations = Http::post($this->url . '/utility/pickuppoints-for-address', array_merge($this->authenticate(), $parameters))->json();

        $mappedLocations = [];

        foreach ($locations['PickUpPoints'] as $location) {
            $mapped = [
                'external_id' => $location['CarrierId'],
                'name' => $location['Name'],
                'slug' => Str::slug($location['Name']),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location['Street'],
                'number' => trim($location['Number'] . ' ' . $location['Addition']),
                'postal_code' => $location['Zipcode'],
                'city' => $location['City'],
                'country' => $location['CountryCode'],
                'telephone' => null,
                'latitude' => $location['GeoLocation']['Latitude'],
                'longitude' => $location['GeoLocation']['Longitude'],
            ];

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
