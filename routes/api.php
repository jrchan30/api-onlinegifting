<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', 'Auth\RegisterController@register');
Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('verification/resend', 'Auth\VerificationController@resend');
Route::post('login', 'Auth\LoginController@login');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', 'UserController@me');
    Route::get('/users', 'UserController@index');
    Route::patch('/users/{id}', 'UserController@update');

    Route::apiResource('userDetails', 'UserDetailController');
    Route::apiResource('transactions', 'TransactionController');
    Route::apiResource('boxes', 'BoxController');
});

Route::apiResource('bundles', 'BundleController');
Route::apiResource('categories', 'CategoryController');
Route::apiResource('products', 'ProductController');
Route::get('/latest-products', 'ProductController@latestProducts');
Route::get('/low-price', 'ProductController@lowPrice');
