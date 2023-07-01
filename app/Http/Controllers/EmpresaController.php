<?php

namespace App\Http\Controllers;

use App\Models\Contribuyente;
use App\Models\Persona;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function ruc($ruc){
        $site = Contribuyente::where('ruc', $ruc)->first();
        if($site){
            $response = [
                'ruc' => $site->ruc,
                'estado' => $site->estado_contribuyente,
                'nombre_o_razon_social' => $site->nombre_razon_social,
                'agente_percepcion' => $site->agenperc_status,
                'agente_retencion' => $site->agenret_status,
                'buen_contribuyente' => $site->buecont_status,
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
    public function dni($dni){
        $personas=Persona::where('dni', $dni)->first();
        if($personas){
            $response = [
                'dni' => $personas->dni,
                'nombre_completo' => $personas->nombre_completo,
                'nombres' => $personas->nombres,
                'apellido_paterno' => $personas->nombre_completo,
                'apellido_materno' => $personas->nombre_completo,

            ];
            return[
                'success' => true,
                'data'=>$response
            ];
        }
        return[
            'success' =>false,
            'message'=> 'El número de DNI no fué localizado.'
        ];
    }
}
