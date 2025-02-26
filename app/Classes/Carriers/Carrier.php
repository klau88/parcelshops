<?php

namespace App\Classes\Carriers;

interface Carrier {
    public function getName();
    public function getIcon();
    public function authenticate();
    public function locations();
}
