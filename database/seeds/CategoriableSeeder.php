<?php

use App\Models\Categoriable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CategoriableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeds/data/categoriables.json");
        // test
        $data = json_decode($json);
        foreach ($data as $obj) {
            Categoriable::create([
                'id' => $obj->id,
                'category_id' => $obj->category_id,
                'categoriable_id' => $obj->categoriable_id,
                'categoriable_type' => $obj->categoriable_type,
            ]);
        }
    }
}
