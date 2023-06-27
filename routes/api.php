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




Route::get('/generate', 'App\Http\controllers\PermissionConstroller@generate');



/**proceso interno agente de retencion */
Route::get('/ar/download', 'App\Http\controllers\AgenteRetencionConstroller@download');
Route::get('/ar/extractor', 'App\Http\controllers\AgenteRetencionConstroller@extractor');
Route::get('/ar/loaddata', 'App\Http\controllers\AgenteRetencionConstroller@loaddata');
Route::get('/ar/proceso_ar', 'App\Http\controllers\AgenteRetencionConstroller@agente_retencion');

/**proceso interno buenos contribuyentes */
Route::get('/bc/download', 'App\Http\controllers\BuenContribuyenteConstroller@download');
Route::get('/bc/extractor', 'App\Http\controllers\BuenContribuyenteConstroller@extractor');
Route::get('/bc/loaddata', 'App\Http\controllers\BuenContribuyenteConstroller@loaddata');
Route::get('/bc/proceso_bc', 'App\Http\controllers\BuenContribuyenteConstroller@buenos_contribuyentes');

/**proceso interno buenos contribuyentes */
Route::get('/ap/download', 'App\Http\controllers\AgentePercepcionConstroller@download');
Route::get('/ap/extractor', 'App\Http\controllers\AgentePercepcionConstroller@extractor');
Route::get('/ap/loaddata', 'App\Http\controllers\AgentePercepcionConstroller@loaddata');
Route::get('/ap/proceso_ap', 'App\Http\controllers\AgentePercepcionConstroller@buenos_contribuyentes');
