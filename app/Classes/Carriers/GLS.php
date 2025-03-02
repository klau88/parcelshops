<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GLS implements Carrier
{
    private $name;
    private $url = 'https://api.gls.nl/V1/api';

    public function __construct()
    {
        $this->name = 'GLS';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function authenticate()
    {
        // TODO: Implement authenticate() method.
    }

    public function locations(array $data): array
    {
        if ($data['limit'] > 10) $data['limit'] = 10;

        $locations = Http::post($this->url . '/ParcelShop/GetParcelShops', [
            'countryCode' => $data['country'],
            'zipCode' => $data['postal'],
            'amountOfShops' => $data['limit'],
            'username' => config('carriers.gls.username'),
            'password' => config('carriers.gls.password'),
        ])->json();

        $mappedLocations = [];

        foreach ($locations['parcelShops'] as $location) {
            $mapped = [
                'external_id' => $location['parcelShopId'],
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'carrier' => $this->name,
                'type' => $location['type'],
                'street' => $location['street'],
                'number' => $location['houseNo'],
                'postal_code' => $location['zipcode'],
                'city' => $location['city'],
                'country' => $location['countryCode'],
                'telephone' => null,
                'latitude' => $location['geoCoordinates']['lat'],
                'longitude' => $location['geoCoordinates']['lng'],
            ];

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
