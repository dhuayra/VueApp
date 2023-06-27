<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
            // $filepath = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgentRet_TXT.zip"));
            $url = "https://www2.sunat.gob.pe/padron_reducido_ruc.zip";
            // $url = "https://ww1.sunat.gob.pe/descarga/AgentRet/AgenRet_TXT.zip";


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
          
            //$ini = 'storage/padron/padron_reducido_ruc.zip';
            //$ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgentRet_TXT.zip"));
            $ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.zip"));
            //$fin = 'storage/padron/';
            //$fin = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR));
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
            
            DB::table('empresa_temporal')->truncate();

            //$file =  'storage/padron/padron_reducido_ruc.txt';
            $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."padron_reducido_ruc.txt"));
            
            $query = "LOAD DATA LOCAL INFILE '" . $file . "'
            INTO TABLE empresa_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
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
                    @created_at,
                    @updated_at)
            SET status=1,created_at=NOW(),updated_at=null";
            DB::connection()->getpdo()->exec($query);


            return [ 'success' => true, 'message' => 'Datos csv cargados a BD correctamente'];

      }catch(Exception $e){
            
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
       
    }

}
