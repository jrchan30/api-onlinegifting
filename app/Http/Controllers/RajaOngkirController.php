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

    public function getServicesCosts(Request $request)
    {
        $destination = $request->input('destination');
        $weight = $request->input('weight');
        $courier = $request->input('courier');

        $form = [
            'origin' => '154',
            'destination' => $destination,
            'weight' => $weight,
            'courier' => $courier,
        ];

        $response = Http::asForm()->withHeaders([
            'key' => config('app.rajaongkir_key')
        ])->post("https://api.rajaongkir.com/starter/cost", $form);

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
