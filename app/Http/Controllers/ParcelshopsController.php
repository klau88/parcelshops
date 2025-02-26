<?php

namespace App\Http\Controllers;

use App\Classes\Carriers\DHL;
use App\Classes\Carriers\DPD;
use App\Classes\Carriers\GLS;
use App\Classes\Carriers\PostNL;
use App\Enums\Carrier;
use App\Models\Parcelshops;
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
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = [];
        $carriers = [];

        foreach (Carrier::cases() as $case) {
            array_push($carriers, $case->name);
            $carrier = $this->selectCarrier($case->name);

            foreach ($carrier->locations() as $location) {
                array_push($locations, $location);
            }
        }

        $defaultMarkerIcon = asset('images/icons/parcelpro-marker.png');

        return Inertia::render('Parcelshops/Map', compact('locations', 'carriers', 'defaultMarkerIcon'));
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
    public function show(Parcelshops $parcelshops)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Parcelshops $parcelshops)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Parcelshops $parcelshops)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Parcelshops $parcelshops)
    {
        //
    }
}
