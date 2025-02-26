<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parcelshops extends Model
{
    /** @use HasFactory<\Database\Factories\ParcelshopsFactory> */
    use HasFactory;

    protected $fillable = [
        'external_id',
        'name',
        'slug',
        'type',
        'street',
        'number',
        'postal_code',
        'city',
        'country',
        'telephone',
        'latitude',
        'longitude',
    ];
}
