<?php

use App\Http\Controllers\BoxController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', 'UserController@register');
Route::post('/login', 'UserController@login');


Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', 'UserController@me');
    Route::get('/users', 'UserController@index');
    Route::patch('/users/{id}', 'UserController@update');

    Route::apiResource('userDetails', 'UserDetailController');
    Route::apiResource('transactions', 'TransactionController');
    Route::apiResource('boxes', 'BoxController');
    Route::apiResource('bundles', 'BundleController');
    Route::apiResource('products', 'ProductController');
    Route::apiResource('categories', 'CategoryController');
});
