<?php

use App\Models\Image;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeds/data/images.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Image::create([
                'id' => $obj->id,
                'path' => $obj->path,
                'url' => $obj->url,
                'imageable_id' => $obj->imageable_id,
                'imageable_type' => $obj->imageable_type,
            ]);
        }
    }
}
