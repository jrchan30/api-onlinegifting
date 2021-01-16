<?php

use App\Models\Detail;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class DetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeds/data/details.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Detail::create([
                'id' => $obj->id,
                'colour' => $obj->colour,
                'design' => $obj->design,
                'detailable_id' => $obj->detailable_id,
                'detailable_type' => $obj->detailable_type,
            ]);
        }
    }
}
