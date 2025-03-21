<?php
declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $user = new User();
        $user->name = "Zachary Craig";
        $user->password = Hash::make("password");
        $user->email = "zack@zcraig.me";
        $user->save();

        $this->call(ITURegionSeeder::class);
        $this->call(DXCCEntitySeeder::class);

    }
}
