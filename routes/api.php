<?php

use App\Beer;
use App\Brewer;
use App\Http\Controllers\BeersController;
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

Route::get('/beers/', function () {
    return Beer::orderBy('name')->paginate(10);
});
//how to check if given filed exists in db?
Route::get('/beers/sortby/{field}', function ($field) {
    abort_unless(in_array($field, array_keys(Beer::first()->attributesToArray())), 400, 'Field does not exist');

    return Beer::orderBy($field)->paginate(10);
});

Route::get('/beers/{field}/{fieldValue}', function ($field, $fieldValue) {
    return Beer::where($field, '=', $fieldValue)->paginate(10);
});

Route::get('/beers/price/{priceFrom}/{priceTo}', function ($priceFrom, $priceTo) {
    // $q = Beer::where('price', '>=', $priceFrom);

    // if (/**/) {
    //     $q->where('price', '<=', $priceTo)
    // }

    // return $q->paginate(25);

    if ((double)$priceFrom > (double)$priceTo) return "Please enter priceFrom > priceTo";
    return Beer::where('price', '>=', $priceFrom)->where('price', '<=', $priceTo)->paginate(25);
});

Route::get('/test', function (BeersController $BC) {
    return $BC->test();
});

Route::get('/brewer/{brewer}', function (Brewer $brewer) {

	//all results are in json format but you can explicitly demand json by using response() -> json()
    return response() -> json([
        'brewer' => $brewer,
        'beers' => $brewer->beers()->count(),
    ]);
});

Route::get('/beer/{beer}', function (Beer $beer) {
    return [
        'beer' => $beer
    ];
});