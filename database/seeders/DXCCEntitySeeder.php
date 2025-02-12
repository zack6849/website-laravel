<?php

namespace Database\Seeders;

use App\Models\DXCCEntity;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DXCCEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = Storage::get('seed/dxcc.json');
        $data = collect(json_decode($json, true)['dxcc'])->where('deleted', '=', false);
        foreach ($data as $entityData){
            DXCCEntity::factory()->create([
                'name' => $entityData['name'],
                'country_code' => $entityData['countryCode'],
            ]);
        }
    }
}
