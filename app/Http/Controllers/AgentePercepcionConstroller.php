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
            DB::table('agentes_percepcion_temporal')->truncate();
            $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.txt"));
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE agentes_percepcion_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\r' IGNORE 1 LINES
                    (ruc,
                    nombre_razon_social,
                    a_partir_del,
                    resolucion,
                    @estado,
                    @created_at,
                    @updated_at)
            SET estado=1,created_at=NOW(),updated_at=null";
            DB::connection()->getpdo()->exec($query);
            $varCR = DB::table('agentes_percepcion_temporal')->count();

            if ($varCR == 0){
                DB::table('agentes_percepcion_temporal')->truncate();
                $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenPerc_TXT.txt"));
                $query = "LOAD DATA LOCAL INFILE '" . $file . "'
                INTO TABLE agentes_percepcion_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                        (ruc,
                        nombre_razon_social,
                        a_partir_del,
                        resolucion,
                        @estado,
                        @created_at,
                        @updated_at)
                SET estado=1,created_at=NOW(),updated_at=null";
                DB::connection()->getpdo()->exec($query);
                $varLF = DB::table('agentes_percepcion_temporal')->count();
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
                                    $query->select('ruc')->from('agentes_percepcion_temporal')->where('estado', '=', 1);
                                })
            ->update(['estado' => 0, 'tipo_carga' => 'BAJA', 'fecha_actualizado' => $update_date]);
        
        //2 En la tabla agentes de percepcion, se agregan los contribuyentes que están en la tabla temporal y que no están en la tabla principal. Los datos agregados son los siguientes:

        DB::table('agentes_percepcion')
            ->insertUsing(
                ['ruc', 'nombre_razon_social', 'a_partir_del', 'resolucion', 'estado', 'tipo_carga', 'fecha_actualizado', 'created_at', 'updated_at'],
                function (Builder $query) use($update_date) {
                    $query->select([ 'ruc', 'nombre_razon_social', 'a_partir_del', 'resolucion', 'estado', DB::raw("'ALTA'"),  DB::raw("'".$update_date. "'" . ' as fecha'), DB::raw('NOW()'), DB::raw('NOW()') ])
                        ->from('agentes_percepcion_temporal')
                        ->where('estado', '=', 1)
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
            ->where('estado', '=', '0')
            ->whereIn('ruc', function ($query) {
                                $query->select('ruc')->from('agentes_percepcion_temporal')->where('estado', '=', 1);
                    })
            ->update(['estado' => '1', 'tipo_carga' => 'RE_ALTA', 'fecha_actualizado' => $update_date]);



        //4 Actualizar tabla contribuyentes  ******************************************************************
        // DB::table('contribuyentes AS c')
        //     ->join('agentes_percepcion AS ap', 'c.ruc', '=', 'ap.ruc')
        //     ->update([
        //         'c.agenperc_status' => DB::raw('ap.status'),
        //         'c.agenperc_apartirdel' => DB::raw('ap.a_partir_del')
        //     ]);



        //5 Asimismo, se modifica el campo “estado” en la tabla de contribuyentes
        DB::table('proceso_log')
            ->insertUsing(['ruc', 'fecha_actualizacion', 'tipo_actualizacion', 'tabla_actualizado', 'created_at', 'updated_at'],
                            function (Builder $query) use($update_date) {
                                $query->select(['ruc', 'fecha_actualizado', 'tipo_carga', DB::raw("'Agentes_Percepción'"), DB::RAW('now()'), DB::RAW('now()')])
                                        ->from('agentes_percepcion')
                                        ->where('fecha_actualizado', '=', $update_date);
                            }
            );
    }

    public function pruebas(){
        // DB::table('personas')
        //     ->update([
        //         'crc32_ruc' => DB::Raw('crc32(ruc)')
        //     ]);

        

        // DB::table('personas_temporal')
        // ->update([
        //     'crc32_personas_temp' => DB::RAW(
        //                         'crc32(concat(ruc,nombre_completo))'
        //                     )
        // ]);

        // $str = "Hello, world! ererer personas_temporal";
        // $crc32Value = crc32($str);
        // $crc32HexValue = sprintf("%08x", $crc32Value);

        // echo "CRC-32 value (hex) of .. '$str' y - '$crc32Value': $crc32HexValue";
        // return;

        DB::table('contribuyentes_temporal')->truncate();
        return 'ok';
    }
    

}
