<?php

use App\Models\Category;
use App\Models\Image;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $cat = Category::all();
        // $subCategories = [];
        // foreach ($cat as $x) {
        //     if (count($x->allSubCategories) < 1) {
        //         array_push($subCategories, $x->id);
        //     }
        // }
        // // $subCategories = Category::whereNotNull('category_id')->pluck('id')->toArray();

        // factory(Product::class, 50)->create()->each(function ($product) use ($subCategories) {
        //     $random = random_int(1, 2);
        //     $product->images()->saveMany(factory(Image::class, $random)->make());
        //     // $product->category()->save(factory(Category::class)->make());
        //     $product->categories()->attach($subCategories[array_rand($subCategories, 1)]);
        // });

        $json = File::get("database/seeds/data/products.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Product::create([
                'id' => $obj->id,
                'name' => $obj->name,
                'description' => $obj->description,
                'price' => $obj->price,
                'weight' => $obj->weight,
                'stock' => $obj->stock,
            ]);
        }
    }
}
