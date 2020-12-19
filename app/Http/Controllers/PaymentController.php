<?php

namespace App\Http\Controllers;

use App\Models\Paid\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    public function notification(Request $request)
    {
        $payload = $request->getContent();
        // return $payload;
        // $notification = json_decode($request);
        $notification = json_decode($payload);

        $validSignatureKey = hash("sha512", $notification->order_id . $notification->status_code . $notification->gross_amount . env('MIDTRANS_SERVER_KEY'));
        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        // $this->initPaymentGateway();
        \Midtrans\Config::$serverKey = config('app.midtrans_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $paymentNotification = new \Midtrans\Notification();
        // $transactionDB = Transaction::where('transaction_number', $paymentNotification->order_id)->firstOrFail();

        // if ($transactionDB->transaction_status == 'settlement' || $transactionDB->transaction_status == 'capture') {
        //     return response(['message' => 'The order has been paid before'], 422);
        // }


        $transaction_mt = $paymentNotification->transaction_status;
        $type = $paymentNotification->payment_type;
        $orderId = $paymentNotification->order_id;
        $fraud = $paymentNotification->fraud_status;

        $vaNumber = null;
        $vendorName = null;
        if (!empty($paymentNotification->va_numbers[0])) {
            $vaNumber = $paymentNotification->va_numbers[0]->va_number;
            $vendorName = $paymentNotification->va_numbers[0]->bank;
        }


        $paymentStatus = null;
        if ($transaction_mt == 'capture') {
            //     // For credit card transaction, we need to check whether transaction is challenge by FDS or not
            if ($type == 'credit_card') {
                if ($fraud == 'challenge') {
                    // TODO set payment status in merchant's database to 'Challenge by FDS'
                    // TODO merchant should decide whether this transaction is authorized or not in MAP
                    $paymentStatus = 'challenge';
                } else {
                    // TODO set payment status in merchant's database to 'Success'
                    $paymentStatus = 'success';
                }
            }
        } else if ($transaction_mt == 'settlement') {
            // TODO set payment status in merchant's database to 'Settlement'
            $paymentStatus = 'settlement';
        } else if ($transaction_mt == 'pending') {
            // TODO set payment status in merchant's database to 'Pending'
            $paymentStatus = 'pending';
        } else if ($transaction_mt == 'deny') {
            // TODO set payment status in merchant's database to 'Denied'
            $paymentStatus = 'deny';
        } else if ($transaction_mt == 'expire') {
            // TODO set payment status in merchant's database to 'expire'
            $paymentStatus = 'expire';
        } else if ($transaction_mt == 'cancel') {
            // TODO set payment status in merchant's database to 'Denied'
            $paymentStatus = 'cancel';
        }

        $str_ord_id = $paymentNotification->order_id;
        $arr_ord_id = explode("/", $str_ord_id);
        $user_id = $arr_ord_id[2];

        $transactionParams = [
            // 'user_id' => Auth::user()->id,
            // 'order_id' => $transactionDB->id,
            // // 'number' => Payment::generateCode(),
            // 'amount' => $paymentNotification->gross_amount,
            // 'method' => 'midtrans',
            // 'status' => $paymentStatus,
            // 'token' => $paymentNotification->transaction_id,
            // 'payloads' => $payload,
            // 'payment_type' => $paymentNotification->payment_type,
            // 'va_number' => $vaNumber,
            // 'vendor_name' => $vendorName,
            // 'biller_code' => $paymentNotification->biller_code,
            // 'bill_key' => $paymentNotification->bill_key,

            'user_id' => $user_id,
            'order_id' => $paymentNotification->order_id,
            'transaction_number' => $paymentNotification->transaction_id,
            // // 'number' => Payment::generateCode(),
            'total_price' => $paymentNotification->gross_amount,
            'method' => 'midtrans',
            'status' => $paymentStatus,
            'token' => $paymentNotification->transaction_id,
            'payloads' => $payload,
            'payment_type' => $paymentNotification->payment_type,
            'va_number' => $vaNumber,
            'vendor_name' => $vendorName,
            'biller_code' => $paymentNotification->biller_code,
            'bill_key' => $paymentNotification->bill_key,

            // 'receiver_phone_number' => '011234556',
            // 'receiver_full_address' => 'lalalalalala',
            // 'receiver_destination_code' => '111',
            // 'total_weight' => 112333,
            // 'delivery_courier_code' => 'jne',
            // 'delivery_courier_service' => 'OKE',
            // 'delivery_fee' => 123111
        ];

        $payment = Transaction::create($transactionParams);

        // if ($paymentStatus && $payment) {
        //     \DB::transaction(
        //         function () use ($order, $payment) {
        //             if (in_array($payment->status, [Payment::SUCCESS, Payment::SETTLEMENT])) {
        //                 $order->payment_status = Order::PAID;
        //                 $order->status = Order::CONFIRMED;
        //                 $order->save();
        //             }
        //         }
        //     );
        // }

        // $message = 'Payment status is : ' . $paymentStatus;
        $message = 'Payment status is : testing';

        $response = [
            'code' => 200,
            'message' => $message,
        ];

        return $request;
        // return response($response, 200);
    }

    public function completed(Request $request)
    {
    }

    public function unfinish(Request $request)
    {
    }

    public function failed(Request $request)
    {
    }
}
