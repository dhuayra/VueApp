<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            DB::table('contribuyentes')->truncate();
            $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE contribuyentes  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
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
                    @status,
                    @agenperc_status,
                    @agenperc_apartirdel,
                    @created_at,
                    @updated_at)
            SET status=1, agenperc_status=null, agenperc_apartirdel=null,created_at=NOW(),updated_at=null";
            DB::connection()->getpdo()->exec($query);
            $varLF = DB::table('contribuyentes')->count();

            if($varLF == 0){
                DB::table('contribuyentes')->truncate();
                $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            
                $query = "LOAD DATA LOCAL INFILE '" . $file . "'
                INTO TABLE contribuyentes  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\r' IGNORE 1 LINES
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
                        @status,
                        @agenperc_status,
                        @agenperc_apartirdel,
                        @created_at,
                        @updated_at)
                SET status=1, agenperc_status=null, agenperc_apartirdel=null,created_at=NOW(),updated_at=null";
                DB::connection()->getpdo()->exec($query);
                $varCR = DB::table('contribuyentes')->count();
                return [ 'success' => true, 'message' => 'nDatos csv cargados a BD correctamente', 'data'=>$varLF];

            }
            return [ 'success' => true, 'message' => 'nDatos csv cargados a BD correctamente', 'data'=>$varLF];
            
        }catch(Exception $e){
            
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
       
    }

    public function updatecontributors(){
        try{
            $ver = "hey";
            return 'heyyyyy';
        }catch(Exception $e){
            return 'nada';
        }
    }

}
