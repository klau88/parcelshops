<?php

namespace App\Http\Controllers;

use App\Classes\Carriers\DHL;
use App\Classes\Carriers\DPD;
use App\Classes\Carriers\GLS;
use App\Classes\Carriers\Homerr;
use App\Classes\Carriers\Intrapost;
use App\Classes\Carriers\PostNL;
use App\Enums\Carrier;
use App\Models\Parcelshop;
use Illuminate\Http\Request;
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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = [];
        $carriers = [];
        $icons = [];

        $data = [
            'latitude' => config('app.default_lat'),
            'longitude' => config('app.default_lng'),
            'postal' => config('app.default_postal'),
            'number' => config('app.default_number'),
            'country' => config('app.default_country') ?? 'NL',
            'limit' => 20
        ];

        foreach (Carrier::cases() as $case) {
            array_push($carriers, $case->name);
            $carrier = $this->selectCarrier($case->name);
            $icons[$case->name] = asset('images/icons/' . strtolower($case->name) . '-marker.png');

            foreach ($carrier->locations($data) as $location) {
                array_push($locations, $location);
            }
        }

        $defaultMarkerIcon = asset('images/icons/parcelpro-marker.png');

        return Inertia::render('Parcelshops/Map', compact('locations', 'carriers', 'icons', 'defaultMarkerIcon'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Parcelshop $parcelshops)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parcelshop $parcelshops)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parcelshop $parcelshops)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parcelshop $parcelshops)
    {
        //
    }
}
