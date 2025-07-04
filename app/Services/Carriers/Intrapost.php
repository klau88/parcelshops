<?php

namespace App\Services\Carriers;

use App\Models\Parcelshop;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class Intrapost implements Carrier
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $url = 'https://api.intrapost.nl';

    public function __construct()
    {
        $this->name = 'Intrapost';
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return PendingRequest
     */
    public function authenticate(): PendingRequest
    {
        return [
            'ApiKey' => config('carriers.intrapost.apiKey'),
            'AccountNumber' => config('carriers.intrapost.accountNumber'),
        ];
    }

    /**
     * @param array $data
     * @return array
     */
    public function locations(array $data): array
    {
        $parameters = [
            'Zipcode' => $data['postal'],
            'Number' => intval($data['number'] ?? 1),
            'Limit' => $data['limit'],
            'CountryCode' => $data['country']
        ];

        $locations = Http::post($this->url . '/utility/pickuppoints-for-address', array_merge($this->authenticate(), $parameters))->json();

        $mappedLocations = [];

        foreach ($locations['PickUpPoints'] as $location) {
            $mapped = [
                'external_id' => $location['CarrierId'],
                'name' => $location['Name'],
                'slug' => Str::slug($location['Name']),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location['Street'],
                'number' => trim($location['Number'] . ' ' . $location['Addition']),
                'postal_code' => $location['Zipcode'],
                'city' => $location['City'],
                'country' => $location['CountryCode'],
                'telephone' => null,
                'latitude' => $location['GeoLocation']['Latitude'],
                'longitude' => $location['GeoLocation']['Longitude'],
                'monday' => null,
                'tuesday' => null,
                'wednesday' => null,
                'thursday' => null,
                'friday' => null,
                'saturday' => null,
                'sunday' => null,
            ];

            foreach ($location['OpeningHours'] as $time) {
                $day = strtolower($time['Day']) ?? null;

                $mapped[$day] = $time['Time'];
            }

            array_push($mappedLocations, $mapped);

            Parcelshop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
