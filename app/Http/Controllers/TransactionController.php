<?php

namespace App\Http\Controllers;

use App\Http\Resources\CartResource;
use App\Http\Resources\TransactionResource;
use App\Models\Box;
use App\Models\Bundle;
use App\Models\Paid\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $s = $request->get('search') ?? '';
        $orderBy = $request->get('orderBy') ?? 'created_at';
        $orderDir = $request->get('orderDir') ?? 'desc';
        $search = '%' . $s . '%';

        $id = auth()->user()->id;
        $user = User::find($id);
        // var_dump($user);
        if (auth()->user()->userDetail->type == 'customer') {
            $transactions = $user->transactions()->where('transaction_number', 'LIKE', $search)
                ->where('receiver_phone_number', 'LIKE', $search)
                ->where('receiver_full_address', 'LIKE', $search)
                ->orderBy($orderBy, $orderDir);
        } else {
            $transactions = Transaction::where('transaction_number', 'LIKE', $search)
                ->orWhere('receiver_phone_number', 'LIKE', $search)
                ->orWhere('receiver_full_address', 'LIKE', $search)
                ->orderBy($orderBy, $orderDir);
        }

        return TransactionResource::collection($transactions->paginate(12));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $this->validate($request, [
            'arrBundles' => 'sometimes|array',
            'arrBoxes' => 'sometimes|array',
            'receiver_phone' => 'required|string',
            'receiver_address' => 'required|string',
            'receiver_city' => 'required',
            'receiver_postal_code' => 'required',
            'courier' => 'required|string',
            'service' => 'required|string',
            'shippingFee' => 'required|numeric',
            'arrival_date' => 'sometimes',
            'buyer_phoneNum' => 'required|string',
            'buyer_address' => 'required|string',
        ]);

        $bundles_id = $validated['arrBundles'];
        $boxes_id = $validated['arrBoxes'];

        $bundles = Bundle::whereIn('id', $bundles_id)->get();

        $boxes = Box::whereIn('id', $boxes_id)->get();

        $totalBundleCost = 0;
        $totalWeight = 0;
        foreach ($bundles as $bundle) {
            $totalBundleCost += $bundle->calculatePrice();
            $totalWeight += $bundle->calculateWeight();
        }

        $totalBoxCost = 0;
        foreach ($boxes as $box) {
            $totalBoxCost += $box->calculatePrice();
            $totalWeight += $box->calculateWeight();
        }

        $shippingFee = $validated['shippingFee'];

        $grandTotal = $totalBundleCost + $totalBoxCost + ((count($boxes) + count($bundles)) * 10000) + $shippingFee;

        $userId = Auth::user()->id;
        $user = User::find($userId);

        $unix = Carbon::now()->timestamp;
        $counterTx = Transaction::count() + 1;
        $randomStr = strtoupper(Str::random(5));
        $txNumber = "INV/{$unix}/{$userId}/{$counterTx}-{$randomStr}";

        \Midtrans\Config::$serverKey = config('app.midtrans_key');
        \Midtrans\Config::$isProduction = false;
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $midtransParams = array(
            'transaction_details' => array(
                'order_id' => $txNumber,
                'gross_amount' => $grandTotal,
            ),
            'customer_details' => array(
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
                'phone' => $validated['buyer_phoneNum'],
                'billing_address' => array(
                    'address' => $validated['buyer_address'],
                ),
                "shipping_address" => array(
                    'phone' => $validated['receiver_phone'],
                    'address' => $validated['receiver_address'],
                    'city' => $validated['receiver_city'],
                    'postal_code' => $validated['receiver_postal_code'],
                    'country_code' => 'IDN'
                )
            ),
        );

        $snapToken = \Midtrans\Snap::getSnapToken($midtransParams);

        $params = array(
            'transaction_number' => $txNumber,
            'user_id' => $userId,
            'receiver_full_address' => $validated['receiver_address'],
            'receiver_phone_number' => $validated['receiver_phone'],
            'receiver_city' => $validated['receiver_city'],
            'receiver_postal_code' => $validated['receiver_postal_code'],
            'total_weight' => $totalWeight,
            'total_price' => $grandTotal,
            'arrivalDate' => $validated['arrival_date'] ?? null,
            'buyer_phone_num' => $validated['buyer_phoneNum'],
            'delivery_fee' => $validated['shippingFee'],
            'delivery_courier_code' => $validated['courier'],
            'delivery_courier_service' => $validated['service'],
            'payment_status' => 'unpaid',
            'token' => $snapToken,
        );

        $transaction = Transaction::create($params);

        $cart = $user->cart()->first();
        if (count($boxes_id) > 0) {
            foreach ($boxes_id as $box_id) {
                $boxToDetach = Box::find($box_id);

                $paidBox = $transaction->paidBoxes()->create([
                    'box_id' => $boxToDetach->id,
                    'name' => $boxToDetach->name,
                    'path' => $boxToDetach->detail->image->path ?? '',
                    'url' => $boxToDetach->detail->image->url ?? '',
                ]);

                $boxToDetachProducts = $boxToDetach->products()->get();

                foreach ($boxToDetachProducts as $product) {
                    $productImg = $product->images()->first();
                    $paidBox->paidProducts()->create([
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'quantity' => $product->pivot->quantity,
                        'weight' => $product->weight,
                        'path' => $productImg->path,
                        'url' => $productImg->url
                    ]);
                    $product->stock = $product->stock - $product->pivot->quantity;
                    $product->save();
                }
            }
            $cart->boxes()->wherePivotIn('cartable_id', $boxes_id)->detach();
        }

        if (count($bundles_id) > 0) {
            foreach ($bundles_id as $bundle_id) {
                $bundleToDetach = Bundle::find($bundle_id);

                $paidBundle = $transaction->paidBundles()->create([
                    'bundle_id' => $bundleToDetach->id,
                    'name' => $bundleToDetach->name,
                    'path' => $bundleToDetach->detail->image->path,
                    'url' => $bundleToDetach->detail->image->url,
                ]);

                $bundleToDetachProducts = $bundleToDetach->products()->get();

                foreach ($bundleToDetachProducts as $product) {
                    $productImg = $product->images()->first();
                    $paidBundle->paidProducts()->create([
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $product->price,
                        'quantity' => $product->pivot->quantity,
                        'weight' => $product->weight,
                        'path' => $productImg->path,
                        'url' => $productImg->url
                    ]);
                    $product->stock = $product->stock - $product->pivot->quantity;
                    $product->save();
                }
                $cart->bundles()->wherePivotIn('cartable_id', $bundles_id)->detach();
            }
        }
        return new TransactionResource($transaction);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        return new TransactionResource($transaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
