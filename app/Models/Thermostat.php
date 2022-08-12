<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Thermostat extends Model
{
    use HasFactory;

    protected $fillable = [
        'online',
        'mode',
        'current_temperature',
        'expected_temperature',
        'humidity',
    ];

    protected $casts = [
        'online' => 'boolean',
    ];
}
