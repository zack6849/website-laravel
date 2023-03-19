<?php

namespace Database\Seeders;

use App\Models\DXCCEntity;
use App\Models\ITURegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DXCCEntitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = collect(json_decode(\Storage::get('seed/dxcc.json'), true)['dxcc'])->where('deleted', '=', false);
        foreach ($data as $entityData){
            DXCCEntity::factory()->create([
                'name' => $entityData['name'],
                'country_code' => $entityData['countryCode'],
            ]);
        }
    }
}
