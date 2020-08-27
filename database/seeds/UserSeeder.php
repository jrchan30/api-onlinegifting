<?php

use App\Models\Box;
use App\Models\Bundle;
use App\Models\User;
use App\Models\Image;
use App\Models\Detail;
use App\Models\Product;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\UserDetail;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();

        factory(User::class, 10)->create()->each(function ($user) use ($products) {
            $user->userDetail()->save(factory(UserDetail::class)->make());
            $user->userDetail->image()->save(factory(Image::class)->make());

            $user->boxes()->saveMany(factory(Box::class, 2)->make());

            $boxes = $user->boxes()->get();
            foreach ($boxes as $box) {
                $box->detail()->save(factory(Detail::class)->make());
                $box->detail->image()->save(factory(Image::class)->make());

                $box->detail->category()->attach(rand(1, 10));

                $box->products()->attach($products->random(rand(2, 5))->pluck('id')->toArray());
                // $box->calculatePrice();
            }

            $user->transactions()->saveMany(factory(Transaction::class, 2)->make());

            $transactions = $user->transactions()->get();
            $bundles = Bundle::all();
            //$boxes = $user->boex()->get();
            foreach ($transactions as $transaction) {
                $rand = random_int(1, 3);

                if ($rand == 1) {
                    //box only
                    $transaction->boxes()->attach($boxes->random(rand(1, 2))->pluck('id')->toArray());
                } else if ($rand == 2) {
                    //bundle only
                    $transaction->bundles()->attach($bundles->random(rand(1, 2))->pluck('id')->toArray());
                } else {
                    //box and bundle
                    $transaction->boxes()->attach($boxes->random(rand(1, 2))->pluck('id')->toArray());
                    $transaction->bundles()->attach($bundles->random(rand(1, 2))->pluck('id')->toArray());
                }
            }
        });
    }
}
