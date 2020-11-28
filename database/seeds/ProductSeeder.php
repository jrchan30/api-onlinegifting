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
        $subCategories = Category::whereNotNull('category_id')->pluck('id')->toArray();

        factory(Product::class, 50)->create()->each(function ($product) use ($subCategories) {
            $random = random_int(1, 2);
            $product->images()->saveMany(factory(Image::class, $random)->make());
            // $product->category()->save(factory(Category::class)->make());
            $product->categories()->attach($subCategories[array_rand($subCategories, 1)]);
        });
    }
}
