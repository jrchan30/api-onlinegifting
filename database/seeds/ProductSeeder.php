<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        factory(Product::class, 10)->create()->each(function ($product) {
            $random = random_int(1, 2);
            $product->images()->saveMany(factory(Image::class, $random)->make());
            // $product->category()->save(factory(Category::class)->make());
            $product->category()->attach(rand(1, 10));
        });
    }
}
