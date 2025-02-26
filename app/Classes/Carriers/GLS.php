<?php

namespace App\Classes\Carriers;

use App\Classes\Carriers\Carrier;
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
        // TODO: Implement authenticate() method.
    }

    public function locations()
    {
        $locations = Http::post($this->url . '/ParcelShop/GetParcelShops', [
            'countryCode' => 'NL',
            'zipCode' => config('app.default_postal'),
            'amountOfShops' => 10,
            'username' => config('carriers.gls.username'),
            'password' => config('carriers.gls.password'),
        ])->json();

        return collect($locations['parcelShops'])->map(fn($location) => [
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
            'icon' => $this->getIcon()
        ])->toArray();
    }
}
