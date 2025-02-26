<?php

namespace App\Classes\Carriers;

use App\Classes\Carriers\Carrier;
use Illuminate\Support\Facades\Http;

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

    public function authenticate()
    {
        // TODO: Implement authenticate() method.
    }

    public function locations()
    {
        $locations = Http::post($this->url . '/ParcelShop/GetParcelShops', [
            'countryCode' => 'NL',
            'zipCode' => config('carriers.gls.postal'),
            'amountOfShops' => 10,
            'username' => config('carriers.gls.username'),
            'password' => config('carriers.gls.password'),
        ])->json();

        return collect($locations['parcelShops'])->map(fn($item) => ['lat' => $item['geoCoordinates']['lat'], 'long' => $item['geoCoordinates']['lng']])->toArray();
    }
}
