<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Bundle;
use App\Models\Product;
use App\Models\Category;
use App\Models\Paid\PaidBox;
use Illuminate\Http\Request;
use App\Models\Paid\Transaction;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BundleResource;
use App\Http\Resources\PaidBoxResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\TransactionResource;

class UserSearchController extends Controller
{
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

        $products = Product::where([['name', 'LIKE', $search], ['stock', '>=', 0]])->get();
        $bundles = Bundle::where('name', 'LIKE', $search)->get();
        $categories = Category::whereNull('category_id')->where('name', 'LIKE', $search)
            ->with('allSubCategories')
            ->get();

        return response()->json([
            'products' => ProductResource::collection($products),
            'bundles' => BundleResource::collection($bundles),
            'categories' => CategoryResource::collection($categories),
        ], 200);
    }
}
