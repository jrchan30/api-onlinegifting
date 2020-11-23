<?php

namespace App\Http\Controllers;

use App\Http\Resources\BundleResource;
use App\Http\Resources\PaidBoxResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\TransactionResource;
use App\Http\Resources\UserResource;
use App\Models\Bundle;
use App\Models\Paid\PaidBox;
use App\Models\Paid\Transaction;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class AdminSearchController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin');
    }
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $this->validate($request, ['search' => 'required|string']);
        $search = '%' . $request->input('search') . '%';

        $products = Product::where('name', 'LIKE', $search)->get();
        $bundles = Bundle::where('name', 'LIKE', $search)->get();
        $transactions = Transaction::where('transaction_number', 'LIKE', $search)->get();
        $paid_box = PaidBox::where('name', 'LIKE', $search)->get();
        $users = User::where('name', 'LIKE', $search)->orWhere('email', 'LIKE', $search)->get();

        return response()->json([
            'products' => ProductResource::collection($products),
            'bundles' => BundleResource::collection($bundles),
            'transactions' => TransactionResource::collection($transactions),
            'paid_box' => PaidBoxResource::collection($paid_box),
            'users' => UserResource::collection($users),
        ], 200);
    }
}
