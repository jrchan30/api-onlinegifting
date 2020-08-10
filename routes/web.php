<?php

use App\Http\Resources\BundleResource;
use App\Http\Resources\TransactionResource;
use App\Models\Box;
use App\Models\Bundle;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::get('/bundles/{id}', function ($id) {
    $bundle = Bundle::findorFail($id);
    return new BundleResource($bundle);
});

Route::get('/boxes/{id}', function ($id) {
    $box = Box::findorFail($id);
    return new BundleResource($box);
});

Route::get('/users/{id}', function ($id) {
    $user = User::findorFail($id);
    return TransactionResource::collection($user->transactions);
});
