<?php

namespace Database\Seeders;

use App\Models\ITURegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ITURegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regions = [
            [
                "id" => 1,
                "name" => "Region 1",
                "description" => "Europe, Africa and the Middle East (also Russia)"
            ], [
                "id" => 2,
                "name" => "Region 2",
                "description" => "The americas and greenland, some of the pacific islands"
            ], [
                "id" => 3,
                "name" => "Region 3",
                "description" => "Most non-former soviet state asian countries, as well as oceania"
            ]
        ];
        foreach ($regions as $data) {
            $region = ITURegion::factory()->create();
            $region->forceFill($data);
            $region->save();
        }
    }
}
