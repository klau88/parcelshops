<?php

namespace App\Services\Carriers;

use App\Models\Parcelshop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Homerr implements Carrier
{
    private $name;
    private $url = 'https://homerr-functions-production.azurewebsites.net/api';

    public function __construct()
    {
        $this->name = 'Homerr';
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
        $locations = Http::get($this->url . '/v1/homerrs/dropoff', [
            'postalcode' => $data['postal'],
            'number' => $data['number'] ?? null,
            'country' => $data['country'],
            'limit' => $data['limit'],
        ])->json();

        $mappedLocations = [];

        foreach ($locations as $location) {
            $mapped = [
                'external_id' => $location['id'],
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location['street'],
                'number' => trim($location['houseNumber'] . ' ' . $location['houseAddition'] . ' ' . $location['houseLetter']),
                'postal_code' => $location['postalCode'],
                'city' => $location['city'],
                'country' => $location['country'],
                'telephone' => null,
                'latitude' => $location['latitude'],
                'longitude' => $location['longitude'],
                'monday' => null,
                'tuesday' => null,
                'wednesday' => null,
                'thursday' => null,
                'friday' => null,
                'saturday' => null,
                'sunday' => null,
            ];

            if ($location['mon_from'] && $location['mon_to']) {
                $mapped['monday'] = Carbon::parse($location['mon_from'])->format('H:i') . '-' . Carbon::parse($location['mon_to'])->format('H:i');
            }

            if ($location['tue_from'] && $location['tue_to']) {
                $mapped['tuesday'] = Carbon::parse($location['tue_from'])->format('H:i') . '-' . Carbon::parse($location['tue_to'])->format('H:i');
            }

            if ($location['wed_from'] && $location['wed_to']) {
                $mapped['wednesday'] = Carbon::parse($location['wed_from'])->format('H:i') . '-' . Carbon::parse($location['wed_to'])->format('H:i');
            }

            if ($location['thu_from'] && $location['thu_to']) {
                $mapped['thursday'] = Carbon::parse($location['thu_from'])->format('H:i') . '-' . Carbon::parse($location['thu_to'])->format('H:i');
            }

            if ($location['fri_from'] && $location['fri_to']) {
                $mapped['friday'] = Carbon::parse($location['fri_from'])->format('H:i') . '-' . Carbon::parse($location['fri_to'])->format('H:i');
            }

            if ($location['sat_from'] && $location['sat_to']) {
                $mapped['saturday'] = Carbon::parse($location['sat_from'])->format('H:i') . '-' . Carbon::parse($location['sat_to'])->format('H:i');
            }

            if ($location['sun_from'] && $location['sun_to']) {
                $mapped['sunday'] = Carbon::parse($location['sun_from'])->format('H:i') . '-' . Carbon::parse($location['sun_to'])->format('H:i');
            }

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
