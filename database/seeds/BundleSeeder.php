<?php

use App\Models\Image;
use App\Models\Bundle;
use App\Models\Category;
use App\Models\Detail;
use App\Models\Product;
use Illuminate\Database\Seeder;

class BundleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = Product::all();
        $categories = Category::all();

        factory(Bundle::class, 50)->create()->each(function ($bundle) use ($products, $categories) {
            $bundle->detail()->save(factory(Detail::class)->make());
            $bundle->detail->image()->save(factory(Image::class)->make());
            $bundle->detail->category()->attach(rand(1, 10));

            $bundle->products()->attach($products->random(rand(2, 5))->pluck('id')->toArray());
            $allProducts = $bundle->products()->get();
            $productsId = $allProducts->pluck('id');

            foreach ($productsId as $id) {
                $bundle->productQuantities()->create([
                    'product_id' => $id,
                    'quantity' => random_int(1, 5)
                ]);
            }
        });
    }
}
