<?php

use App\Models\Productable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class ProductableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $json = File::get("database/seeds/data/productables.json");
        $data = json_decode($json);
        foreach ($data as $obj) {
            Productable::create([
                'id' => $obj->id,
                'product_id' => $obj->product_id,
                'productable_id' => $obj->productable_id,
                'productable_type' => $obj->productable_type,
                'quantity' => $obj->quantity,
            ]);
        }
        //test
    }
}
