<?php

namespace App\Services\Carriers;

use Illuminate\Http\Client\PendingRequest;

interface Carrier
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return PendingRequest
     */
    public function authenticate(): PendingRequest;

    /**
     * @param array $data
     * @return array
     */
    public function locations(array $data): array;
}
