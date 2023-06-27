<?php


namespace App\Http\Controllers;

use Goutte\Client;
use Illuminate\Http\Request;

class ScraperController extends Controller
{
    //
    private $result = array();

    public function scraper(){
        $client = new Client();
        $url = 'https://www.worldometers.info/coronavirus/';
        $page = $client->request('GET', $url);

        /*echo "<pre>";
        print_r($page);*/

        // echo $page->filter('.maincounter-number')->text();

        $page->filter('#maincounter-wrap')->each(function ($item) {
            $this->results[$item->filter('h1')->text()] = $item->filter('.maincounter-number')->text();
        });

        $data = $this->results;
        //return view('scraper', compact('data'));
        return $data;
    }

    public function exchange(){
        $client = new Client();
        $url = 'https://www.sbs.gob.pe/app/pp/sistip_portal/paginas/publicacion/tipocambiopromedio.aspx';
        $page = $client->request('GET', $url);

        /*echo "<pre>";
        print_r($page);*/

        // echo $page->filter('.maincounter-number')->text();

        $page->filter('.APLI_fila3')->each(function ($item) {
            $this->results[$item->filter('p')->text()] = $item->filter('.APLI_fila2')->text();
        });

        $data = $this->results;
        //return view('scraper', compact('data'));
        return $data;


    }

    public function index()
{
    $url = 'https://www.sbs.gob.pe/app/pp/sistip_portal/paginas/publicacion/tipocambiopromedio.aspx';
    $tipos_cambio = [];

    $client = new Client();
    $crawler = $client->request('GET', $url);

    $table = $crawler->filter('.APLI_conteTabla2_new table')->eq(0);

    $tableRows = $table->filter('tbody tr');

    if ($tableRows->count() > 0) {
        $tableRows->each(function ($row) use (&$tipos_cambio) {
            $descripcion = trim($row->filter('td')->eq(0)->text());
            $compra = trim($row->filter('td')->eq(1)->text());
            $venta = trim($row->filter('td')->eq(2)->text());

            $tipos_cambio[] = [
                'descripcion' => $descripcion,
                'compra' => $compra,
                'venta' => $venta
            ];
        });
    }

    return response()->json($tipos_cambio);
}



}
