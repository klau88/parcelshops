<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DHL implements Carrier
{
    private $name;
    private $url = 'https://api-gw.dhlparcel.nl';

    public function __construct()
    {
        $this->name = 'DHL';
    }

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return asset('images/icons/' . strtolower($this->name) . '-marker.png');
    }

    public function authenticate()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Token' => config('carriers.dhl.token')
        ]);
    }

    public function locations()
    {
        $locations = $this->authenticate()
            ->get($this->url . '/parcel-shop-locations/NL?postalCode=' . config('app.default_postal'))
            ->json();

        $mappedLocations = [];

        foreach ($locations as $location) {
            $mapped = [
                'external_id' => $location['id'],
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'carrier' => $this->name,
                'type' => $location['shopType'],
                'street' => $location['address']['street'],
                'number' => $location['address']['number'],
                'postal_code' => $location['address']['zipCode'],
                'city' => $location['address']['city'],
                'country' => $location['address']['countryCode'],
                'telephone' => null,
                'latitude' => $location['geoLocation']['latitude'],
                'longitude' => $location['geoLocation']['longitude'],
            ];

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
