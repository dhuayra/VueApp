<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;

class ContribuyenteConstroller extends Controller
{
    //
    public function padron_reducido()
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
            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.zip"));
            $url = "https://www2.sunat.gob.pe/padron_reducido_ruc.zip";

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
            $ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.zip"));
            
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
            DB::table('contribuyentes_temporal')->truncate();
            $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE contribuyentes_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                    (ruc,
                    nombre_razon_social,
                    estado_contribuyente,
                    condicion_domicilio,
                    ubigeo,
                    tipo_via,
                    nombre_via,
                    codigo_zona,
                    tipo_zona,
                    numero,
                    interior,
                    lote,
                    departamento,
                    manzana,
                    kilometro,
                    @estado,
                    @created_at,
                    @updated_at)
            SET estado=1, created_at=NOW(),updated_at=null";
            DB::connection()->getpdo()->exec($query);
            $varLF = DB::table('contribuyentes_temporal')->count();

            if($varLF == 0){
                DB::table('contribuyentes_temporal')->truncate();
                $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            
                $query = "LOAD DATA LOCAL INFILE '" . $file . "'
                INTO TABLE contribuyentes_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\r' IGNORE 1 LINES
                        (ruc,
                        nombre_razon_social,
                        estado_contribuyente,
                        condicion_domicilio,
                        ubigeo,
                        tipo_via,
                        nombre_via,
                        codigo_zona,
                        tipo_zona,
                        numero,
                        interior,
                        lote,
                        departamento,
                        manzana,
                        kilometro,
                        @estado,
                        @created_at,
                        @updated_at)
                SET estado=1, created_at=NOW(),updated_at=null";
                DB::connection()->getpdo()->exec($query);
                $varCR = DB::table('contribuyentes_temporal')->count();
                return [ 'success' => true, 'message' => 'rDatos csv cargados a BD correctamente', 'data'=>$varCR];

            }
            return [ 'success' => true, 'message' => 'nDatos csv cargados a BD correctamente', 'data'=>$varLF];
            
        }catch(Exception $e){
            
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
       
    }

    public function updatecontributors(){

        $update_date = now();
        
        DB::table('contribuyentes')
            ->insertUsing(
                ['ruc', 'nombre_razon_social', 'estado_contribuyente', 'condicion_domicilio', 'ubigeo', 'tipo_via',
                    'nombre_via', 'codigo_zona', 'tipo_zona', 'numero', 'interior', 'lote', 'departamento', 'manzana', 'kilometro', 
                    'estado', 'agenperc_estado', 'agenret_estado', 'buecont_estado', 'fecha_actualizado', 'created_at', 'updated_at'],
                function (Builder $query) use($update_date){
                    $query->select(['ruc', 'nombre_razon_social', 'estado_contribuyente', 'condicion_domicilio', 'ubigeo', 'tipo_via',
                                    'nombre_via', 'codigo_zona', 'tipo_zona', 'numero', 'interior', 'lote', 'departamento', 'manzana', 'kilometro', 'estado',
                                    DB::raw('null'), DB::RAW('null'), DB::RAW('null'), DB::raw("'".$update_date. "'" . ' as fecha'), DB::raw('now()'), DB::RAW('now()')])
                            ->from('contribuyentes_temporal')
                            ->where('estado', '=', 1)
                            ->whereNotIn('ruc', function ($query){
                                                    $query->select('ruc')->from('contribuyentes');
                                        })
                                ;
                }
            );
        
            return 'terminó';
    }

    /** Actualizar Personas Naturales */
    public function update_person(){
        $update_date = now();
        /** #1 Cargar datos de personas naturales.
         * Esta funcionalidad carga una tabla temporal de personas naturales
         * a partir de la tabla de contribuyentes, 
         * para identificar a las personas naturales se toma en cuenta los RUCs que comienzan con 10. 
         * Considerar que el RUC tiene la siguiente estructura
         * 
         * 
         * 
         */
        DB::table('personas_temporal')
            ->insertUsing(
                ['ruc', 'nombre_completo', 'nombre_auxiliar', 'update_date', 'created_at', 'updated_at'],
                function (Builder $query) use($update_date){
                    $query->select(['ruc', 'nombre_razon_social', 'nombre_razon_social', DB::raw("'".$update_date. "'" . ' as fecha'), DB::raw('NOW()'), DB::raw('NOW()')])
                            ->from('contribuyentes')
                            ->where('ruc','like','10%')
                            ->limit(10000)
                            ;
                }
            );
        
        /**Actualizaciones en la tabla temporal de personas */
        DB::table('personas_temporal')
            ->update([
                'dni' => DB::RAW('substring(ruc,3,8)')
            ]);

        DB::table('personas_temporal')
            ->update([
                'apellido_paterno' => DB::raw("SUBSTRING(nombre_completo, 1, INSTR(nombre_completo, ' '))"),
                'nombre_auxiliar' => DB::raw("SUBSTRING(nombre_completo, INSTR(nombre_completo, ' ') + 1)")
            ]);
        
        DB::table('personas_temporal')
            ->update([
                'apellido_materno' => DB::raw("SUBSTRING(nombre_auxiliar, 1, INSTR(nombre_auxiliar, ' '))")
            ]);
        
        DB::table('personas_temporal')
            ->update([
                'nombre_auxiliar' => DB::raw("SUBSTRING(nombre_auxiliar, INSTR(nombre_auxiliar, ' ') + 1)")
            ]);
        
        DB::table('personas_temporal')
            ->update([
                'nombres' => DB::raw("nombre_auxiliar")
            ]);

        
        /** #2 Actualización de datos
         * Cuando se complete la carga de datos en la tabla temporal de personas, este proceso, 
         * actualizará la tabla principal con los nuevos datos de personas naturales encontradas en la tabla temporal
         */
        DB::table('personas')
            ->insertUsing(
                ['dni', 'ruc', 'nombre_completo', 'nombres', 'apellido_paterno', 'apellido_materno', 'update_date', 'created_at', 'updated_at'],
                function (Builder $query_update) use($update_date){
                    $query_update->select(['dni', 'ruc', 'nombre_completo', 'nombres', 'apellido_paterno', 'apellido_materno', DB::raw("'".$update_date. "'" . ' as fecha'), DB::raw('NOW()'), DB::raw('NOW()')])
                    ->from('prueba')
                    ->whereNotIn('ruc', function ($query){
                                            $query->select('ruc')->from('personas');

                    });
                }
            );

        return 'cempletado';

    }



}
