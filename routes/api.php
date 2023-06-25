<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('categoria', 'App\Http\controllers\EmployeeController@getEmployee');
Route::get('categoria/{id}', 'App\Http\controllers\EmployeeController@getEmployeeId');

//api_sunat- RUC
Route::get('/ruc/{ruc}','App\Http\controllers\EmpresaController@ruc');

/** Orden para ejecutar la acualización de datos de la sunat. Ult Act. 11-08-2022 */
Route::get('/padron/download', 'App\Http\controllers\PadronController@download');
Route::get('/padron/extractor', 'App\Http\controllers\PadronController@extractor');
Route::get('/padron/loaddata', 'App\Http\controllers\PadronController@loaddata');
