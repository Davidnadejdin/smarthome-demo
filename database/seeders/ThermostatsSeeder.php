<?php

namespace Database\Seeders;

use App\Models\Thermostat;
use Illuminate\Database\Seeder;

class ThermostatsSeeder extends Seeder
{
    public function run()
    {
        Thermostat::factory()->count(2)->create();
    }
}
