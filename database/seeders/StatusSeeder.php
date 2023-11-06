<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status')->insert([
            'temp_current' => 259,
            'sensor_list' => 'AQEBAQEBAA==',
            'ph_current' => 410,
            'ph_warn_max' => 0,
            'ph_warn_min' => 0,
            'temp_warn_max' => 350,
            'temp_warn_min' => 100,
            'tds_current' => 0,
            'tds_warn_max' => 0,
            'tds_warn_min' => 0,
            'ec_current' => 0,
            'ec_warn_max' => 0,
            'ec_warn_min' => 0,
            'salinity_current' => 0,
            'salinity_warn_max' => 0,
            'salinity_warn_min' => 0,
            'pro_current' => 996,
            'pro_warn_max' => 500,
            'pro_warn_min' => 500,
            'orp_current' => 0,
            'orp_warn_max' => -2000,
            'orp_warn_min' => -2000,
            'cf_current' => 0,
            'cf_warn_max' => 0,
            'cf_warn_min' => 1,
            'rh_current' => 0,
            'rh_warn_max' => 0,
            'rh_warn_min' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
