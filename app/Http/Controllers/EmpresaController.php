<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function ruc($ruc){
        $site = Empresa::where('ruc', $ruc)->first();
        if($site){
            $response = [
                'ruc' => $site->ruc,
                'estado' => $site->estado_contribuyente,
                'nombre_o_razon_social' => $site->nombre_razon_social,
            ];
            return[
                'success' => true,
                'data' => $response
            ];
        }
        return [
            'success' => false,
            'message'=> "El número de RUC no fué encontrado."
        ];
    }
    // public function getEmployeeId($id){
    //     $categoria = Employee::find($id);
    //     if(is_null($categoria)){
    //         return response()->json(['Mensaje'=>'Nada '], 404);

    //     }
    //     return response()->json($categoria::find($id), 200);
    // }
}
