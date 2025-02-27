<?php

namespace App\Enums;

enum Carrier : string
{
    case PostNL = 'PostNL';
    case DHL = 'DHL';
    case DPD = 'DPD';
    case GLS = 'GLS';
    case Intrapost = 'Intrapost';
    case Homerr = 'Homerr';
}
