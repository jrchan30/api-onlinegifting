<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transaction = Transaction::where('user_id', auth()->user()->id)->latest()->paginate(10);

        return TransactionResource::collection($transaction);
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
            'delivery_fee' => 'required|numeric',
            'total_price' => 'required|numeric',
            'receiver_location' => 'required|numeric',
            'arrival_date' => 'required|date'
        ]);

        $transaction = Transaction::create($validated);

        return new TransactionResource($transaction);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id == auth()->user()->id) {
            return new TransactionResource($transaction);
        } else {
            return response()->json(['error' => 'Forbidden'], 403);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Transaction $transaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Transaction  $transaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(Transaction $transaction)
    {
        // if($transaction->user_id == auth()->user()->id){
        //     $transaction->delete();
        //     return response()->json('Successfully Deleted', 204);
        // }else{
        //     return response()->json(['Error' => 'Forbidden Not Your Transaction'], 403);
        // }
    }
}
