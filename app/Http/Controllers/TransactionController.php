<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransactionResource;
use App\Models\Paid\Transaction;
use Illuminate\Http\Request;

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
        $transactions = Transaction::where('transaction_number', 'LIKE', $search)
            ->orWhere('receiver_phone_number', 'LIKE', $search)
            ->orWhere('receiver_full_address', 'LIKE', $search)
            ->orderBy($orderBy, $orderDir);
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
        //
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
