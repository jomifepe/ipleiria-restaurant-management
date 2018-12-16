<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', 'LoginControllerAPI@login')->name('login');

Route::middleware('auth:api')->post('logout', 'LoginControllerAPI@logout');
Route::middleware('auth:api')->get('users/me', 'UserControllerAPI@myProfile');

Route::post('register', 'UserControllerAPI@store')->name('register');

Route::get('items', 'ItemControllerAPI@index');

Route::apiResources(['meals'    => 'MealControllerAPI',
                     'invoices' => 'InvoiceControllerAPI',
                     'orders'   => 'OrderControllerAPI',
                     'items'    => 'ItemControllerAPI',
                     'users'    => 'UserControllerAPI']);

Route::get('meals/waiter/{waiterId}', 'MealControllerAPI@responsible')->name('meal.waiter');
Route::get('meals/{mealId}/items', 'OrderControllerAPI@mealItems')->name('meal.orders.items');



Route::post('tables', 'TableControllerAPI@store');
Route::get('tables', 'TableControllerAPI@index');
Route::put('table/{id}', 'TableControllerAPI@update');
Route::delete('table/delete/{id}', 'TableControllerAPI@destroy');
//Route::delete('table/softDelete/{id}', 'TableControllerAPI@softDelete');


