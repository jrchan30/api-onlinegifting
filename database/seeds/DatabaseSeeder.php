<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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

        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('123123123'),
        ]);

        DB::table('user_details')->insert([
            'user_id' => 1,
            'type' => 'admin',
            'address' => 'Jln. Pulo Nangka Timur IB',
            'phone_num' => '081514329539,'
        ]);

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            BundleSeeder::class,
            UserSeeder::class,
        ]);
    }
}
