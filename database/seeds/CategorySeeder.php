<?php

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(Category::class, 10)->create();
        $json = File::get("database/seeds/data/categories.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Category::create([
                'id' => $obj->id,
                'name' => $obj->name,
                'category_id' => $obj->category_id,
            ]);
        }
    }
}
