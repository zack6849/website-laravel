<?php

use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $user = new User();
        $user->name = "Zachary Craig";
        $user->password = Hash::make("password");
        $user->email = "zack@zack6849.com";
        $user->save();
    }
}
