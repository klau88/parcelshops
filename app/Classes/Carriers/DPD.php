<?php

namespace App\Classes\Carriers;

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

    public function getName()
    {
        return $this->name;
    }

    public function getIcon()
    {
        return asset('images/icons/' . strtolower($this->name) . '-marker.png');
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

    public function locations()
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
        $lat = config('app.default_lat');
        $lng = config('app.default_lng');

        $locations = $parcelshopClient->__soapCall('findParcelShopsByGeoData', [
            [
                'longitude' => substr((string)$lng, 0, 10),
                'latitude' => substr((string)$lat, 0, 10),
                'limit' => 10
            ]
        ]);

        return collect($locations->parcelShop)->map(fn($location) => [
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
            'telephone' => $location->phone ?? null,
            'email' => $location->email ?? null,
            'latitude' => $location->latitude,
            'longitude' => $location->longitude,
            'icon' => $this->getIcon()
        ])->toArray();
    }
}
