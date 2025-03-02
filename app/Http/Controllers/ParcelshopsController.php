<?php

namespace App\Http\Controllers;

use App\Classes\Carriers\DHL;
use App\Classes\Carriers\DPD;
use App\Classes\Carriers\GLS;
use App\Classes\Carriers\Homerr;
use App\Classes\Carriers\Intrapost;
use App\Classes\Carriers\PostNL;
use App\Enums\Carrier;
use App\Classes\Carriers\Carrier as CarrierInterface;
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
            $icons[$carrier->name] = asset('images/icons/' . strtolower($carrier->name) . '-marker.png');
        }

        return $icons;
    }

    public function locations(): array
    {
        $parameters = [
            'latitude' => request()->latitude ?? config('app.default_lat'),
            'longitude' => request()->longitude ?? config('app.default_lng'),
            'postal' => request()->postal ?? config('app.default_postal'),
            'number' => request()->number ?? config('app.default_number'),
            'country' => request()->country ?? config('app.default_country') ?? 'NL',
            'limit' => 20
        ];

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
        return Inertia::render('Parcelshops/Map', [
            'locations' => $this->locations(),
            'carriers' => array_column(Carrier::cases(), 'name'),
            'icons' => $this->getIcons(),
            'defaultMarkerIcon' => asset('images/icons/parcelpro-marker.png'),
            'latitude' => request()->latitude ?? config('app.default_lat'),
            'longitude' => request()->longitude ?? config('app.default_lng'),
            'postal' => request()->postal ?? config('app.default_postal'),
            'number' => request()->number ?? config('app.default_number'),
            'country' => request()->country ?? config('app.default_country') ?? 'NL',
            'selectedCarrier' => request()->carrier ?? null,
        ]);
    }
}
