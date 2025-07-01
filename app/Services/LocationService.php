<?php

namespace App\Services;

use App\Enums\Carrier;
use App\Services\Carriers\DHL;
use App\Services\Carriers\DPD;
use App\Services\Carriers\GLS;
use App\Services\Carriers\Homerr;
use App\Services\Carriers\Intrapost;
use App\Services\Carriers\PostNL;
use Illuminate\Support\Facades\Vite;

class LocationService
{
    private function selectCarrier(string $carrier)
    {
        switch ($carrier) {
            case 'PostNL':
                return new PostNL();
            case 'GLS':
                return new GLS();
            case 'DPD':
                return new DPD();
            case 'DHL':
                return new DHL();
            case 'Intrapost':
                return new Intrapost();
            case 'Homerr':
                return new Homerr();
        }
    }

    public function getIcons()
    {
        $icons = [];

        foreach (Carrier::cases() as $carrier) {
            $icons[$carrier->name] = Vite::asset('resources/images/icons/' . strtolower($carrier->name) . '-marker.png');
        }

        return $icons;
    }

    public function locations(): array
    {
        $parameters = request()->all() ?: [
            'latitude' => config('app.default_lat'),
            'longitude' => config('app.default_lng'),
            'postal' => config('app.default_postal'),
            'number' => config('app.default_number'),
            'country' => config('app.default_country') ?? 'NL',
        ];

        $parameters['limit'] = 20;
        $carriers = array_filter(array_column(Carrier::cases(), 'name'), fn($carrier) => request()->carrier === null || $carrier === request()->carrier);

        $locations = [];

        foreach ($carriers as $carrier) {
            $parcelshops = $this->selectCarrier($carrier)->locations($parameters);

            foreach ($parcelshops as $parcelshop) {
                array_push($locations, $parcelshop);
            }
        }

        return $locations;
    }
}
