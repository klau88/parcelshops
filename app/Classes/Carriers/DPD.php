<?php

namespace App\Classes\Carriers;

use App\Models\Parcelshop;
use Illuminate\Support\Str;
use SoapClient;
use SoapHeader;

class DPD implements Carrier
{
    private $name;
    private $url = 'https://wsshipper.dpd.nl/soap/WSDL';
    private const MESSAGE_LANGUAGE = 'nl_NL';

    public function __construct()
    {
        $this->name = 'DPD';
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function authenticate()
    {
        $authClient = new SoapClient($this->url . '/LoginServiceV21.wsdl', [
            'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]
            )
        ]);

        $authData = [
            'delisId' => config('carriers.dpd.delisId'),
            'password' => config('carriers.dpd.password'),
            'messageLanguage' => self::MESSAGE_LANGUAGE,
        ];

        return $authClient->getAuth($authData);
    }

    public function locations(array $data): array
    {
        $parcelshopClient = new SoapClient($this->url . '/ParcelShopFinderServiceV50.wsdl', [
            'stream_context' => stream_context_create([
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false
                    ]
                ]
            )
        ]);

        $soapHeader = new SoapHeader('http://dpd.com/common/service/types/Authentication/2.0', 'authentication', [
            'delisId' => config('carriers.dpd.delisId'),
            'authToken' => $this->authenticate()->return->authToken,
            'messageLanguage' => self::MESSAGE_LANGUAGE,
        ]);

        $parcelshopClient->__setSoapHeaders([$soapHeader]);

        $locations = $parcelshopClient->__soapCall('findParcelShopsByGeoData', [
            [
                'latitude' => substr((string)$data['latitude'], 0, 10),
                'longitude' => substr((string)$data['longitude'], 0, 10),
                'limit' => $data['limit']
            ]
        ]);

        $mappedLocations = [];

        foreach ($locations->parcelShop as $location) {
            $mapped = [
                'external_id' => $location->parcelShopId,
                'name' => $location->company,
                'slug' => Str::slug($location->company),
                'carrier' => $this->name,
                'type' => $this->name,
                'street' => $location->street,
                'number' => $location->houseNo,
                'postal_code' => $location->zipCode,
                'city' => $location->city,
                'country' => $location->country,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ];

            if ($location->phone) {
                $mapped['phone'] = $location->phone;
            }

            if ($location->email) {
                $mapped['email'] = $location->email;
            }

            array_push($mappedLocations, $mapped);

            ParcelShop::firstOrCreate($mapped);
        }

        return $mappedLocations;
    }
}
