<?php

namespace App\Services\Carriers;

use App\Models\Parcelshop;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DHL implements Carrier
{
    private $name;
    private $url = 'https://api-gw.dhlparcel.nl';

    public function __construct()
    {
        $this->name = 'DHL';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function authenticate()
    {
        return Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Token' => config('carriers.dhl.token')
        ]);
    }

    public function locations(array $data): array
    {
        $locations = $this->authenticate()
            ->get($this->url . '/parcel-shop-locations/NL?postalCode=' . $data['postal'] . '&limit=' . $data['limit'])
            ->json();

        $mappedLocations = [];

        foreach ($locations as $location) {
            $times = array_map(fn($time) => Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i'), $location['openingTimes']);

            $mapped = [
                'external_id' => $location['id'],
                'name' => $location['name'],
                'slug' => Str::slug($location['name']),
                'carrier' => $this->name,
                'type' => $location['shopType'],
                'street' => $location['address']['street'],
                'number' => $location['address']['number'],
                'postal_code' => $location['address']['zipCode'],
                'city' => $location['address']['city'],
                'country' => $location['address']['countryCode'],
                'telephone' => null,
                'latitude' => $location['geoLocation']['latitude'],
                'longitude' => $location['geoLocation']['longitude'],
                'monday' => null,
                'tuesday' => null,
                'wednesday' => null,
                'thursday' => null,
                'friday' => null,
                'saturday' => null,
                'sunday' => null,
            ];

            foreach ($location['openingTimes'] as $time) {
                if ($time['weekDay'] === 1) {
                    $mapped['monday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 2) {
                    $mapped['tuesday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 3) {
                    $mapped['wednesday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 4) {
                    $mapped['thursday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 5) {
                    $mapped['friday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 6) {
                    $mapped['saturday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }

                if ($time['weekDay'] === 7) {
                    $mapped['sunday'] = Carbon::parse($time['timeFrom'])->format('H:i') . '-' . Carbon::parse($time['timeTo'])->format('H:i');
                }
            }

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
