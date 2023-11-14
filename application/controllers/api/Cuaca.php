<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
//To Solve File REST_Controller not found
require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Cuaca extends CI_Controller {
    use REST_Controller {
        REST_Controller::__construct as private __resTraitConstruct;
    }

    function __construct()
    {
        parent::__construct();
        $this->__resTraitConstruct();
    }

    public function cuaca_get() {
        $lat = "-6.912046719944685";
        $lon = "107.62512317606293";
        $appid = "1b43d3bd5300fca93a5d9c411cb37caa";
        $lang = "id";
        if (isset($_GET['lat'])) $lat = $_GET['lat'];
        if (isset($_GET['lon'])) $lon = $_GET['lon'];
        if (isset($_GET['lang'])) $lang = $_GET['lang'];
        $url = "api.openweathermap.org/data/2.5/forecast?lat=" . $lat . "&lon=" . $lon . "&appid=" . $appid . "&units=metric&lang=" . $lang;
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",

        ));
        $response = curl_exec($curl);
        $responseObj = json_decode($response);
        $ramalan = [];
        foreach($responseObj->list as $list) {
            $tgl_forecast = $list->dt_txt;
            $main_weather = $list->weather[0]->main;
            $description_weather = $list->weather[0]->description;
            $temperature = $list->main->feels_like;
            $dataTemp = [
                'tanggal' => $tgl_forecast,
                'cuaca' => $main_weather,
                'deskripsi cuaca' => $description_weather,
                'temperatur' => $temperature . "°"
            ];
            array_push($ramalan, $dataTemp);
        }
        $data = [
            'Lokasi' => $responseObj->city->name,
            'Ramalan Cuaca' => $ramalan
        ];
        curl_close($curl);
        $this->response($data, 200);
    }

}
