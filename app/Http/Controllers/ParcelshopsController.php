<?php

namespace App\Http\Controllers;

use App\Enums\Carrier;
use App\Enums\Country;
use App\Services\LocationService;
use Illuminate\Support\Facades\Vite;
use Inertia\Inertia;

class ParcelshopsController extends Controller
{
    public function index()
    {
        $locationService = new LocationService();

        $countries = array_map(fn($country) => [
            'text' => $country->value,
            'value' => $country->name
        ], Country::cases());

        $carriers = array_map(fn($carrier) => [
            'text' => $carrier->value,
            'value' => $carrier->name
        ], Carrier::cases());

        return Inertia::render('Parcelshops/Map', [
            'locations' => $locationService->locations(),
            'carriers' => $carriers,
            'icons' => $locationService->getIcons(),
            'defaultMarkerIcon' => Vite::asset('resources/images/icons/parcelpro-marker.png'),
            'latitude' => floatval(request()->latitude ?? config('app.default_lat')),
            'longitude' => floatval(request()->longitude ?? config('app.default_lng')),
            'postal' => request()->postal ?? config('app.default_postal'),
            'number' => request()->number ?? config('app.default_number'),
            'country' => request()->country ?? config('app.default_country') ?? 'NL',
            'countries' => $countries,
            'selectedCarrier' => request()->carrier ?? null,
        ]);
    }

    public function locations()
    {
        $locationService = new LocationService();

        return $locationService->locations();
    }
}
