<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new UserResource(Auth::user());
});

Route::post('register', 'Auth\RegisterController@register');
Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('verification/resend', 'Auth\VerificationController@resend');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', 'UserController@me');
    Route::get('/users', 'UserController@index');
    Route::patch('/users/{id}', 'UserController@update');

    Route::apiResource('user-details', 'UserDetailController');
    Route::apiResource('carts', 'CartController');
    Route::apiResource('boxes', 'BoxController');
    Route::apiResource('likes', 'LikeController');
    Route::apiResource('discussions', 'DiscussionController');
    Route::apiResource('replies', 'ReplyController');

    Route::get('/trashed-products', 'ProductController@trashedProducts');

    Route::get('/get-widgets', "DashboardController@getWidgets");
});

Route::apiResource('bundles', 'BundleController');
Route::apiResource('categories', 'CategoryController');
Route::apiResource('products', 'ProductController');
Route::get('/latest-products', 'ProductController@latestProducts');
Route::get('/low-price', 'ProductController@lowPrice');


Route::post('/logout', "Auth\LoginController@logoutAPI");

// Route::get('/product-discussions/{id}', 'DiscussionController@productDiscussions');
// Route::get('/bundle-discussions/{id}', 'DiscussionController@bundleDiscussions');
