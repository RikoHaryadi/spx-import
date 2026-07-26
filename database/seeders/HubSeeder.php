<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
  public function run(): void
{
    \App\Models\Hub::insert([

        [

            'hub_code'=>'KTB2',

            'hub_name'=>'Kota Baru 2 Hub',

            'city'=>'Kotabaru',

            'region'=>'Kalimantan Selatan',

            'is_active'=>1,

        ],

        [

            'hub_code'=>'BJB',

            'hub_name'=>'Banjarbaru Hub',

            'city'=>'Banjarbaru',

            'region'=>'Kalimantan Selatan',

            'is_active'=>1,

        ],

        [

            'hub_code'=>'BJM',

            'hub_name'=>'Banjarmasin Hub',

            'city'=>'Banjarmasin',

            'region'=>'Kalimantan Selatan',

            'is_active'=>1,

        ],

    ]);
}
}
