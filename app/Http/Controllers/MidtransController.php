<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MidtransController extends Controller
{
    public function getToken(Request $request)
    {
        \Midtrans\Config::$serverKey = config('app.midtrans_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        // $total_price = foreach($request->)

        // $params = array(
        //     'transaction_details' => array(
        //         'order_id' => rand(),
        //         'gross_amount' => $request->get('total_price'),
        //     ),
        //     'customer_details' => array(
        //         'name' => Auth::user()->name,
        //         'email' => Auth::user()->email,
        //         // 'phone' => Auth::user()->phone,
        //     ),
        // );

        $userId = Auth::user()->id;

        $unix = Carbon::now()->timestamp;
        $randomStr = rand();
        $orderNumber = "ORD/{$unix}/{$userId}/{$randomStr}";

        $params = array(
            'transaction_details' => array(
                'order_id' => $orderNumber,
                'gross_amount' => 10000,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => Auth::user()->userDetail->phone_num,
            ),
        );

        // $params = array(
        //     'transaction_details' => array(
        //         'order_id' => rand(),
        //         'gross_amount' => 10000,
        //     ),
        //     'customer_details' => array(
        //         'first_name' => 'budi',
        //         'last_name' => 'pratama',
        //         'email' => 'budi.pra@example.com',
        //         'phone' => '08111222333',
        //     ),
        // );


        $snapToken = \Midtrans\Snap::getSnapToken($params);

        return $snapToken;
    }
}
