<?php

namespace App\Services\Carriers;

interface Carrier
{
    public function getName(): string;

    public function authenticate();

    public function locations(array $data): array;
}
