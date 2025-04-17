<?php

namespace App\Http\Controllers;

use App\Classes\Carriers\DHL;
use App\Classes\Carriers\DPD;
use App\Classes\Carriers\GLS;
use App\Classes\Carriers\Homerr;
use App\Classes\Carriers\Intrapost;
use App\Classes\Carriers\PostNL;
use App\Enums\Carrier;
use App\Enums\Country;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;

class ParcelshopsController extends Controller
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

    private function getIcons()
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

    public function index()
    {
        $countries = array_map(fn($country) => [
            'text' => $country->value,
            'value' => $country->name
        ], Country::cases());

        $carriers = array_map(fn($carrier) => [
            'text' => $carrier->value,
            'value' => $carrier->name
        ], Carrier::cases());

        return Inertia::render('Parcelshops/Map', [
            'locations' => $this->locations(),
            'carriers' => $carriers,
            'icons' => $this->getIcons(),
            'defaultMarkerIcon' => Vite::asset('resources/images/icons/parcelpro-marker.png'),
            'latitude' => floatval(request()->latitude ?? config('app.default_lat')),
            'longitude' => floatval(request()->longitude ?? config('app.default_lng')),
            'postal' => request()->postal ?? config('app.default_postal'),
            'number' => intval(request()->number ?? config('app.default_number')),
            'country' => request()->country ?? config('app.default_country') ?? 'NL',
            'countries' => $countries,
            'selectedCarrier' => request()->carrier ?? null,
        ]);
    }
}
