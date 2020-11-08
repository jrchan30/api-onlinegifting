<?php

use App\Models\Box;
use App\Models\Bundle;
use App\Models\Cart;
use App\Models\User;
use App\Models\Image;
use App\Models\Detail;
use App\Models\Product;
use App\Models\Category;
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

        factory(User::class, 20)->create()->each(function ($user) use ($products) {
            $user->userDetail()->save(factory(UserDetail::class)->make());
            $user->userDetail->image()->save(factory(Image::class)->make());

            $user->boxes()->saveMany(factory(Box::class, 2)->make());

            $boxes = $user->boxes()->get();
            foreach ($boxes as $box) {
                $box->detail()->save(factory(Detail::class)->make());
                $box->detail->image()->save(factory(Image::class)->make());

                $box->detail->category()->attach(rand(1, 10));

                $box->products()->attach($products->random(rand(2, 5))->pluck('id')->toArray());
                $allProducts = $box->products()->get();
                $productsId = $allProducts->pluck('id');

                foreach ($productsId as $id) {
                    $box->boxProductQuantities()->create([
                        'product_id' => $id,
                        'quantity' => random_int(1, 5)
                    ]);
                }
                // $box->calculatePrice();
            }

            $user->cart()->save(factory(Cart::class)->make());

            $cart = $user->cart()->first();
            $bundles = Bundle::all();

            $rand = random_int(1, 3);

            if ($rand == 1) {
                //box only
                $cart->boxes()->attach($boxes->random(rand(1, 2))->pluck('id')->toArray());
            } else if ($rand == 2) {
                //bundle only
                $cart->bundles()->attach($bundles->random(rand(1, 2))->pluck('id')->toArray());
            } else {
                //box and bundle
                $cart->boxes()->attach($boxes->random(rand(1, 2))->pluck('id')->toArray());
                $cart->bundles()->attach($bundles->random(rand(1, 2))->pluck('id')->toArray());
            }

            $cartBoxesTotalPrice = 0;
            $cartBundlesTotalPrice = 0;
            if ($cart->boxes()->exists()) {
                $boxesInCart = $cart->boxes()->get();
                foreach ($boxesInCart as $boxInCart) {
                    $rows = $boxInCart->boxProductQuantities()->get();
                    foreach ($rows as $row) {
                        $productPrice = $row->product->price;
                        $totalProductPrice = $row->quantity * $productPrice;

                        $cartBoxesTotalPrice += $totalProductPrice;
                    }
                }
            }
            if ($cart->bundles()->exists()) {
                $bundlesInCart = $cart->bundles()->get();
                foreach ($bundlesInCart as $bundleInCart) {
                    $sumBundlePrice = $bundleInCart->products()->sum('price');
                    $cartBundlesTotalPrice += $sumBundlePrice;
                }
            }

            $deliveryFee = $cart->delivery_fee;
            $cart->total_price = $cartBoxesTotalPrice + $cartBundlesTotalPrice + $deliveryFee;
            $cart->save();
        });
    }
}
