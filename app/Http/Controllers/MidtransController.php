<?php

namespace App\Http\Controllers;

use App\Http\Resources\BoxResource;
use App\Http\Resources\BundleResource;
use App\Models\Box;
use App\Models\Bundle;
use App\Models\Paid\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MidtransController extends Controller
{
    public function getToken(Request $request)
    {
        \Midtrans\Config::$serverKey = config('app.midtrans_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $bundles_id = $request->input('arrBundles');
        $boxes_id = $request->input('arrBoxes');

        $bundles = Bundle::whereIn('id', $bundles_id)->get();

        $boxes = Box::whereIn('id', $boxes_id)->get();

        $totalBundleCost = 0;
        foreach ($bundles as $bundle) {
            $totalBundleCost += $bundle->calculatePrice();
        }

        $totalBoxCost = 0;
        foreach ($boxes as $box) {
            $totalBoxCost += $box->calculatePrice();
        }

        $shippingFee = $request->input('shippingFee');

        $grandTotal = $totalBundleCost + $totalBoxCost + ((count($boxes) + count($bundles)) * 10000) + $shippingFee;

        // $userId = Auth::user()->id;

        // $unix = Carbon::now()->timestamp;
        // $randomStr = rand();
        // $orderNumber = "ORD/{$unix}/{$userId}/{$randomStr}";


        $userId = Auth::user()->id;

        $unix = Carbon::now()->timestamp;
        $counterTx = Transaction::count() + 1;
        $randomStr = strtoupper(Str::random(5));
        $txNumber = "INV/{$unix}/{$userId}/{$counterTx}-{$randomStr}";

        // return $request->input('shipping_address.address');

        $params = array(
            'transaction_details' => array(
                'order_id' => $txNumber,
                'gross_amount' => $grandTotal,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->userDetail->phone_num,
                'billing_address' => array(
                    'address' => Auth::user()->userDetail->address,
                ),
                "shipping_address" => array(
                    'phone' => $request->input('shipping_address.phone'),
                    'address' => $request->input('shipping_address.address'),
                    'city' => $request->input('shipping_address.city'),
                    'postal_code' => $request->input('shipping_address.postal_code'),
                    'country_code' => 'IDN'
                )
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return $snapToken;
    }
}
