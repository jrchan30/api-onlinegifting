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
            $product->categories()->saveMany(factory(Category::class, $random)->make());
        });
    }
}
