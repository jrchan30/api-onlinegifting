<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        $response = Http::asForm()->withHeaders([
            'key' => config('app.rajaongkir_key')
        ])->get("https://api.rajaongkir.com/starter/province");

        return $response;
    }

    public function getCities(Request $request)
    {
        $province = $request->get('province') ?? '';

        $response = Http::asForm()->withHeaders([
            'key' => config('app.rajaongkir_key')
        ])->get("https://api.rajaongkir.com/starter/city?province={$province}");

        return $response;
    }
}
