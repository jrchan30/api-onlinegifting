<?php

namespace App\Http\Controllers;

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
            'buyer_phoneNum' => 'required|string'
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

        $unix = Carbon::now()->timestamp;
        $counterTx = Transaction::count() + 1;
        $randomStr = strtoupper(Str::random(5));
        $txNumber = "INV/{$unix}/{$userId}/{$counterTx}-{$randomStr}";

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
            'status' => 'unpaid',
        );

        // return $params;

        $transaction = Transaction::create($params);

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
