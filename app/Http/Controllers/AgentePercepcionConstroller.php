<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
use App\Models\AgentePercepcion;
use App\Models\ProcesoLog;

class AgentePercepcionConstroller extends Controller
{
    
    //
    public function agente_percepcion()
    {
        $result = $this->download();
        if ($result['success']) {
            $result = $this->extractor();
            if ($result['success']) {
                $result = $this->loaddata();
                if ($result['success']) {
                    return "El proceso se completó correctamente";
                } else {
                    return "Error en el proceso de carga de datos: " . $result['message'];
                }
            } else {
                return "Error en el proceso de extracción: " . $result['message'];
            }
        } else {
            return "Error en la descarga: " . $result['message'];
        }
    }

    public function download()
    {
        try {

            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.zip"));
            $url = "https://ww3.sunat.gob.pe/descarga/AgentRet/AgenPerc_TXT.zip";


            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            $raw_file_data = curl_exec($ch);

            if(curl_errno($ch)){
                return 'error';
            }
            curl_close($ch);
   
            file_put_contents($filepath, $raw_file_data);

            return [ 'success' => true, 'message' => 'Datos descargados de sunat correctamente'];

        }catch(Exception $e)
        {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function extractor()
    {        
        try{
          
            $ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.zip"));
            $fin = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR));
    
            $data = \App\Traits\ExtractorTrait::extract($ini, $fin);
         
            return [ 'success' => true, 'message' => 'Extraccion de zip correctamente', 'data' => $data];

        }catch(Exception $e)
        {
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
       
    }

    public function loaddata()
    {
        try{
            DB::table('ap_temporal')->truncate();
            $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.txt"));
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE ap_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\r' IGNORE 1 LINES
                    (ruc,
                    nombre_razon_social,
                    a_partir_del,
                    resolucion,
                    @status,
                    @created_at,
                    @updated_at)
            SET status=1,created_at=NOW(),updated_at=null";
            DB::connection()->getpdo()->exec($query);
            $varCR = DB::table('ap_temporal')->count();

            if ($varCR == 0){
                DB::table('ap_temporal')->truncate();
                $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.txt"));
                $query = "LOAD DATA LOCAL INFILE '" . $file . "'
                INTO TABLE ap_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                        (ruc,
                        nombre_razon_social,
                        a_partir_del,
                        resolucion,
                        @status,
                        @created_at,
                        @updated_at)
                SET status=1,created_at=NOW(),updated_at=null";
                DB::connection()->getpdo()->exec($query);
                $varLF = DB::table('ap_temporal')->count();
                return [ 'success' => true, 'message' => 'nDatos csv cargados a BD correctamente', 'data'=>$varLF];
            }
            return [ 'success' => true, 'message' => 'rDatos csv cargados a BD correctamente', 'data'=>$varCR];

        }catch(Exception $e){
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
    }


    public function update_perceptionagents(){

        //1 Desactivar registros log en la tabla de buenos contribuyentes
        // DB::table('agentes_percepcion')
        // ->where('log', '1')
        // ->update(['log' => '0']);        
        $update_date = now();

        //1 En la tabla agentes de percepcion, se identifican los contribuyentes que no figuran en la tabla temporal, actualizando los siguientes campos.
            // estado = inactivo(0)
            // fecha_actualización = (la fecha en que se realiza la actualización)
            // tipo_actualización = baja
                
        DB::table('agentes_percepcion')
            ->whereNotIn('ruc', function ($query) {
                                    $query->select('ruc')->from('ap_temporal')->where('status', '=', 1);
                                })
            ->update(['status' => 0, 'type' => 'BAJA', 'update_date' => $update_date]);
        
        //2 En la tabla agentes de percepcion, se agregan los contribuyentes que están en la tabla temporal y que no están en la tabla principal. Los datos agregados son los siguientes:

        DB::table('agentes_percepcion')
            ->insertUsing(
                ['ruc', 'nombre_razon_social', 'a_partir_del', 'resolucion', 'status', 'type', 'update_date', 'created_at', 'updated_at'],
                function (Builder $query) use($update_date) {
                    $query->select([ 'ruc', 'nombre_razon_social', 'a_partir_del', 'resolucion', 'status', DB::raw("'ALTA'"),  DB::raw("'".$update_date. "'" . ' as fecha'), DB::raw('NOW()'), DB::raw('NOW()') ])
                        ->from('ap_temporal')
                        ->where('status', '=', 1)
                        ->whereNotIn('ruc', function ($query) {
                                                $query->select('ruc')->from('agentes_percepcion');
                                            });
                }
            );

        //3 En la tabla agentes de percepcion, se identifican los contribuyentes registrados como inactivos, que existen en la tabla temporal, y se actualiza los siguientes campos:
            //  estado = activo
            //  fecha_actualización = (fecha de modificación) 
            //  tipo_actualización = re_activo

        DB::table('agentes_percepcion')
            ->where('status', '=', '0')
            ->whereIn('ruc', function ($query) {
                                $query->select('ruc')->from('ap_temporal')->where('status', '=', 1);
                    })
            ->update(['status' => '1', 'type' => 'RE_ALTA', 'update_date' => $update_date]);

        //4 Actualizar tabla contribuyentes  ******************************************************************
        DB::table('contribuyentes')
            ->whereIn('ruc', function ($query) {
                                $query->select('ruc')->from('agentes_percepcion')->where('status', '=', 1);
                    })
            ->update(['agenperc_status' => '1', 'agenperc_apartirdel' => '0', 'update_date' => $update_date]);


        //5 Asimismo, se modifica el campo “estado” en la tabla de contribuyentes
        DB::table('proceso_log')
            ->insertUsing(['ruc', 'fecha_actualizacion', 'tipo_actualizacion', 'tabla_actualizado'],
                            function (Builder $query) use($update_date) {
                                $query->select(['ruc', 'update_date', 'type', 'resolucion'])
                                        ->from('agentes_percepcion')
                                        ->where('update_date', '=', $update_date);
                            }
            );
    }
    

}
