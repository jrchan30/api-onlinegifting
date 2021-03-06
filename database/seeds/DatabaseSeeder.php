<?php

use App\Models\Image;
use Carbon\Carbon;
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
            'username' => 'admin1',
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123123123'),
            'created_at' => Carbon::now(),
        ]);

        DB::table('user_details')->insert([
            'user_id' => 1,
            'type' => 'admin',
            'address' => 'Jln. Pulo Nangka Timur IB',
            'phone_num' => '081514329539'
        ]);

        DB::table('carts')->insert([
            'user_id' => 1,
        ]);

        DB::table('users')->insert([
            'username' => 'russ30',
            'name' => 'Jonathan Russell',
            'email' => 'jrussellchan2000@gmail.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('123123123'),
            'created_at' => Carbon::now(),
        ]);

        DB::table('user_details')->insert([
            'user_id' => 2,
            'type' => 'customer',
            'address' => 'Jln. Pulo Nangka Timur IB',
            'phone_num' => '081514329539'
        ]);

        DB::table('carts')->insert([
            'user_id' => 2,
        ]);

        DB::table('rooms')->insert([
            'user_id' => 2,
            'admin_id' => 1,
        ]);

        $this->call([
            CategorySeeder::class,
            CategoriableSeeder::class,
            ProductSeeder::class,
            BundleSeeder::class,
            DetailSeeder::class,
            ImageSeeder::class,
            ProductableSeeder::class,
            // UserSeeder::class,
            // TransactionSeeder::class
        ]);
    }
}
