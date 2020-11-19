<?php

namespace App\Http\Controllers;

use App\Models\Paid\Transaction;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('check.admin');
    }

    public function getWidgets()
    {
        // New Users
        $user_current_month_count = User::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();

        if (Carbon::now()->month == 1) {
            $user_last_month_count = User::whereYear('created_at', Carbon::now()->year - 1)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        } else {
            $user_last_month_count = User::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        }

        if ($user_last_month_count == 0) {
            $user_percentage = 0;
        } else {
            $user_percentage = round((($user_current_month_count - $user_last_month_count) / $user_last_month_count * 100), 2);
        }

        // New Products
        $product_current_month_count = Product::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();

        if (Carbon::now()->month == 1) {
            $product_last_month_count = Product::whereYear('created_at', Carbon::now()->year - 1)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        } else {
            $product_last_month_count = Product::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        }

        if ($product_last_month_count == 0) {
            $product_percentage = 0;
        } else {
            $product_percentage = round((($product_current_month_count - $product_last_month_count) / $product_last_month_count * 100), 2);
        }

        // Transaction
        $transaction_current_month_count = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->count();

        if (Carbon::now()->month == 1) {
            $transaction_last_month_count = Transaction::whereYear('created_at', Carbon::now()->year - 1)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        } else {
            $transaction_last_month_count = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->count();
        }

        if ($transaction_last_month_count == 0) {
            $transaction_percentage = 0;
        } else {
            $transaction_percentage = round((($transaction_current_month_count - $transaction_last_month_count) / $transaction_last_month_count * 100), 2);
        }

        // Transaction Amount
        $transaction_amount_current_month_sum = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->month)->sum('total_price');

        if (Carbon::now()->month == 1) {
            $transaction_amount_last_month_sum = Transaction::whereYear('created_at', Carbon::now()->year - 1)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->sum('total_price');
        } else {
            $transaction_amount_last_month_sum = Transaction::whereYear('created_at', Carbon::now()->year)->whereMonth('created_at', Carbon::now()->subMonth()->format('m'))->sum('total_price');
        }

        if ($transaction_amount_last_month_sum == 0) {
            $transaction_amount_percentage = 0;
        } else {
            $transaction_amount_percentage = round((($transaction_amount_current_month_sum - $transaction_amount_last_month_sum) / $transaction_amount_last_month_sum * 100), 2);
        }

        return response()->json([
            'user' => [
                'current_month_count' => $user_current_month_count,
                'last_month_count' => $user_last_month_count,
                'percentage' => $user_percentage,
            ],

            'product' => [
                'current_month_count' => $product_current_month_count,
                'last_month_count' => $product_last_month_count,
                'percentage' => $product_percentage,
            ],

            'transaction' => [
                'current_month_count' => $transaction_current_month_count,
                'last_month_count' => $transaction_last_month_count,
                'percentage' => $transaction_percentage,
            ],

            'transaction_amount' => [
                'current_month_sum' => $transaction_amount_current_month_sum,
                'last_month_sum' => $transaction_amount_last_month_sum,
                'percentage' => $transaction_amount_percentage,
            ]
        ]);
    }

    public function monthlySales()
    {
        $month_sales = DB::table('transactions')->get(['total_price', 'created_at'])->groupBy(function ($date) {
            return Carbon::parse($date->created_at)->format('m');
        });
        $sums = $month_sales->mapWithKeys(function ($group, $key) {
            return [$key => $group->sum('total_price')];
        });

        return $sums;
    }
}
