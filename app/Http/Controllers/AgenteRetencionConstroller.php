<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgenteRetencionConstroller extends Controller
{
    //
    public function agente_retencion()
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

            $filepath = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenRet_TXT.zip"));
            $url = "https://ww3.sunat.gob.pe/descarga/AgentRet/AgenRet_TXT.zip";


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
          
            $ini = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenRet_TXT.zip"));
            $fin = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR));
    
            $data = \App\Traits\ExtractorTrait::extract($ini, $fin);
         
            return [ 'success' => true, 'message' => 'Extraccion de zip correctamente', 'data' => $data];

        }catch(Exception $e)
        {
            return [ 'success' => false, 'message' => $e->getMessage()];
        }
       
    }

    public function convertir_codificacion(){

        $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar/AgenRet_TXT.txt"));

        // Leer el contenido del archivo
        $texto = file_get_contents($file);

        // Realizar la conversión de codificación y formato de línea
        $texto_convertido = str_replace("\r", "\n", $texto);

        file_put_contents($file, $texto_convertido);

        return ['success' => true];

    }

    public function loaddata()
    {
        $cod_file = $this->convertir_codificacion();
        if($cod_file['success']){
            try{
                DB::table('ar_temporal')->truncate();
                $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenRet_TXT.txt"));

                $query = "LOAD DATA LOCAL INFILE '" . $file . "'
                INTO TABLE ar_temporal  FIELDS TERMINATED BY '|' LINES TERMINATED BY '\n' IGNORE 1 LINES
                        (ruc,
                        nombre_razon_social,
                        a_partir_del,
                        resolucion)
                        ";
                DB::connection()->getpdo()->exec($query);
                
                return [ 'success' => true, 'message' => 'Datos csv cargados a BD correctamente'];

            }catch(Exception $e){
                return [ 'success' => false, 'message' => $e->getMessage()];
            }
        }else{
            return 'Error en la decodificacion del archivo'.$cod_file['message'];
        }
       
    }
    // public function loaddata()
    // {
    //     try {
    //         DB::table('ar_temporal')->truncate();

    //         $file = str_replace(DIRECTORY_SEPARATOR, '/', public_path("padron_rar".DIRECTORY_SEPARATOR."AgenRet_TXT1.txt"));

    //         $handle = fopen($file, "r");
    //         $lineCount = 0;

    //         if ($handle) {
    //             while (($line = fgets($handle)) !== false) {

    //                 $lineCount++;

    //                 if ($lineCount === 1) {
    //                     continue; // IGNORE 1 LINES
    //                 }

    //                 $fields = explode('|', $line);

    //                 $data = [
    //                     'ruc' => $fields[0],
    //                     'nombre_razon_social' => $fields[1],
    //                     // 'a_partir_del' => $fields[2],
    //                     // 'resolucion' => $fields[3],
    //                 ];

    //                 DB::table('ar_temporal')->insert($data);
    //             }

    //             fclose($handle);

    //             return ['success' => true, 'message' => 'Datos csv cargados a BD correctamente'];
    //         }

    //         return ['success' => false, 'message' => 'No se pudo abrir el archivo'];

    //     } catch (Exception $e) {
    //         return ['success' => false, 'message' => $e->getMessage()];
    //     }
    // }
}
