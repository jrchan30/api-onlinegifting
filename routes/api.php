<?php

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new UserResource(Auth::user());
});

Route::post('register', 'Auth\RegisterController@register');
Route::post('verification/verify/{user}', 'Auth\VerificationController@verify')->name('verification.verify');
Route::post('verification/resend', 'Auth\VerificationController@resend');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::post('/payments/notification', 'PaymentController@notification');
Route::get('/payments/completed', 'PaymentController@completed');
Route::get('/payments/failed', 'PaymentController@failed');
Route::get('/payments/unfinish', 'PaymentController@unfinish');


Route::get('/reviews', 'ReviewController@index');

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', 'UserController@me');
    Route::get('/users', 'UserController@index');
    Route::patch('/users/{id}', 'UserController@update');
    Route::get('/admins', 'UserController@admins');
    Route::delete('/users/{id}', 'UserController@destroy');

    Route::apiResource('user-details', 'UserDetailController');
    Route::apiResource('carts', 'CartController');
    Route::apiResource('boxes', 'BoxController');
    Route::apiResource('likes', 'LikeController');
    Route::apiResource('discussions', 'DiscussionController');
    Route::apiResource('replies', 'ReplyController');
    Route::apiResource('messages', 'MessageController');

    Route::post('/reviews', 'ReviewController@store');

    Route::apiResource('transactions', 'TransactionController');
    Route::post('/arrive', 'TransactionController@arrive');

    Route::get('/trashed-products', 'ProductController@trashedProducts');
    Route::post('/restore-product/{id}', 'ProductController@restoreProduct');

    Route::get('/trashed-bundles', 'BundleController@trashedBundles');
    Route::post('/restore-bundle/{id}', 'BundleController@restoreBundle');

    Route::get('/get-widgets', "DashboardController@getWidgets");
    Route::get('/monthly-sales', "DashboardController@monthlySales");
    Route::get('/transactions-count', "DashboardController@transactionsCount");

    Route::post('/admin-search', "AdminSearchController");

    Route::get('/all-products', 'ProductController@allProducts');

    Route::post('/checkout', 'MidtransController@getToken');

    Route::get('/get-provinces', 'RajaOngkirController@getProvinces');
    Route::get('/get-cities', 'RajaOngkirController@getCities');
    Route::post('/get-services-costs', 'RajaOngkirController@getServicesCosts');
});

Route::post('/user-search', "UserSearchController");
Route::apiResource('bundles', 'BundleController');
Route::apiResource('categories', 'CategoryController');
Route::apiResource('products', 'ProductController');
Route::get('/latest-products', 'ProductController@latestProducts');
Route::get('/low-price', 'ProductController@lowPrice');


Route::post('/logout', "Auth\LoginController@logoutAPI");

// Route::get('/product-discussions/{id}', 'DiscussionController@productDiscussions');
// Route::get('/bundle-discussions/{id}', 'DiscussionController@bundleDiscussions');
