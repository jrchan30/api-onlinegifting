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

        factory(Bundle::class, 10)->create()->each(function ($bundle) use ($products) {
            $bundle->detail()->save(factory(Detail::class)->make());
            $bundle->detail->image()->save(factory(Image::class)->make());
            $bundle->detail->categories()->saveMany(factory(Category::class, 2)->make());

            $bundle->products()->attach($products->random(rand(2, 5))->pluck('id')->toArray());

            // $bundle->calculatePrice();
        });
    }
}
