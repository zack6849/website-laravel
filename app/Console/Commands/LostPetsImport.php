<?php

namespace App\Console\Commands;

use App\Models\LostPet;
use App\Providers\GeocoderProvider;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Console\Command\Command as CommandAlias;

class LostPetsImport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lost-pets:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports lost pets from petco love lost website';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $geocoder = resolve(GeocoderProvider::class);
        $response = $geocoder->geocode(config('services.petco.location'));
        $data = data_get($response, 'results.0', false);
        $petCount = 0;
        for ($i = 0; $i < 90; $i++) {
            $pets = collect(
                $this->getPage($data['lat'],
                    $data['lon'],
                    $i,
                    config('services.petco.search_radius')
                )
            )->filter(function($item){
                return $item['typeKey'] != 'search.headerRow.foundPetNearbyList';
            });
            $thisPageCount = count($pets);
            foreach ($pets as $pet) {
                $this->createOrUpdatePet($pet);
                $petCount++;
            }
            $this->info("Page $i: $thisPageCount pets");
            sleep(10);
        }
        $this->info("Imported $petCount pets");
        return CommandAlias::SUCCESS;
    }

    private function getPage($lat, $long, $page, $type = ['Cat'], $radius = 100)
    {
        $response = Http::asJson()->withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0',
            'X-Parse-Application-Id' => config('services.petco.application_id'),
            'X-Parse-Session-Token' => config('services.petco.session_token')
        ])->post('https://frbackendprod.herokuapp.com/parse/functions/getSearchSet', [
            'objects' => [
                'geopoint' => [
                    'latitude' => $lat,
                    'longitude' => $long,
                ]
            ],
            'parameters' => [
                'appVersion' => '100000',
                'facialEngine' => 'MLFacial',
                'feedSetTypeKey' => 'getNearbyList',
                'filters' => $type,
                'listDistance' => "$radius",
                'mlFacialParameters' => [
                    'paging' => [
                        'limit' => 25,
                        'skip' => 0,
                    ],
                ],
                'mode' => 'list',
                'osname' => 'webVue',
                'pageNum' => $page,
                'typeKeys' => [
                    0 => 'foundPet',
                    1 => 'foundOrgPet'
                ]
            ]
        ]);
        return $response->json('result.results.objects.items');
    }

    private function createOrUpdatePet($pet) : void
    {
        match ($pet['typeKey']) {
            'foundPet' => $this->importFoundPet($pet),
            'foundOrgPet' => $this->importFoundOrgPet($pet),
            default => $this->info("Unknown type: {$pet['typeKey']}")
        };
    }


    private function importFoundPet($pet){
        return LostPet::query()->firstOrCreate([
            'animal_id' => data_get($pet, 'id')
        ], [
            'name' => data_get($pet, 'name', 'Unknown Name'),
            'breed' => data_get($pet, '', 'Unknown Breed'),
            'color' => data_get($pet, 'targetEntity.attributes.FDRAnimalPrimaryColor', 'Unknown Color'),
            'sex' => data_get($pet, 'attributes.FDRPetSex', 'Unknown Sex'),
            'photo' => data_get($pet, 'photo.url'),
            'age' => data_get($pet, 'targetEntity.attributes.FDRAnimalAge'),
            'age_group' => data_get($pet, 'targetEntity.attributes.FDRAnimalAgeGroup', 'Unknown Age Group'),
            'status' => data_get($pet, 'targetEntity.status', 'Unknown Status'),
            'rescue_name' => data_get($pet, 'fromEntity.name', 'Unknown Name'),
            'rescue_address' => data_get($pet, 'fromEntity.attributes.locationAddress'),
            'rescue_email' => trim(data_get($pet, 'fromEntity.attributes.FDREntityEmail')),
            'rescue_phone' => data_get($pet, 'fromEntity.attributes.FDREntityPhone'),
            'intake_date' => Carbon::parse(data_get($pet, 'attributes.FDRPetPosterStartDate')),
            'intake_type' => data_get($pet, 'targetEntity.attributes.FDRAnimalIntakeTypeKey'),
            'poster_text' => data_get($pet, 'attributes.FDRPetPosterMarkings')
        ]);
    }

    private function importFoundOrgPet($pet){
        return LostPet::query()->firstOrCreate([
            'animal_id' => data_get($pet, 'id')
        ], [
            'name' => data_get($pet, 'targetEntity.name', 'No name'),
            'breed' => data_get($pet, 'targetEntity.attributes.FDRDogPrimaryBreed', 'Unknown Breed'),
            'color' => data_get($pet, 'targetEntity.attributes.FDRAnimalPrimaryColor', 'Unknown Color'),
            'sex' => data_get($pet, 'targetEntity.attributes.FDRPetSex', 'Unknown'),
            'photo' => data_get($pet, 'targetEntity.photoOriginal.url'),
            'age' => data_get($pet, 'targetEntity.attributes.FDRAnimalAge'),
            'age_group' => data_get($pet, 'targetEntity.attributes.FDRAnimalAgeGroup', 'Unknown Age Group'),
            'status' => data_get($pet, 'targetEntity.status', 'Unknown Status'),
            'rescue_name' => data_get($pet, 'fromEntity.name', 'No name'),
            'rescue_email' => trim(data_get($pet, 'fromEntity.attributes.FDREntityEmail')),
            'rescue_phone' => data_get($pet, 'fromEntity.attributes.FDREntityPhone'),
            'intake_date' => Carbon::parse(data_get($pet, 'targetEntity.attributes.FDRAnimalIntakeDate')),
            'intake_type' => data_get($pet, 'targetEntity.attributes.FDRAnimalIntakeTypeKey'),
        ]);
    }
}
