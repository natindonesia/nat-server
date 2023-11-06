<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\Waterpool;
use App\Models\SensorData;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class WaterpoolController extends Controller
{   
    // inisiasi 
    private $baseUrl;
    private $clientId;
    private $secret;
    private $device_id;

    public function __construct()
    {
        $this->baseUrl = env('POSTMAN_URL', 'https://openapi.tuyaus.com');
        $this->clientId = env('POSTMAN_CLIENT_ID', 'uaamr7n8gka4wackpjn4');
        $this->secret = env('POSTMAN_SECRET', '03170d0703ec4a39a68cbacb46162ed5');
        $this->device_id = env('POSTMAN_DEVICE_ID', 'eb1c6c1de1431103f2wug6');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $status = Waterpool::all();
        return view('waterpool/table-status', compact('status'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    function getAccessToken() {
        $apiUrl = 'https://openapi.tuyaus.com/v1.0/token?grant_type=1';
        $clientId = 'uaamr7n8gka4wackpjn4';
        $secret = '85AC72C82625EBE3F754D210BAA67A0982F67ECED3FFA5203E5A7A3516EF95A2';
        $timestamp = 	round(microtime(true) * 1000);

    
        // Hitung signature
        $stringToSign = "GET\n\n\n" . $timestamp . "\n/v1.0/token?grant_type=1";
        $sign = hash_hmac('sha256', $stringToSign, $secret);
    
        // Kirim permintaan HTTP dengan Laravel HTTP Client
        $response = Http::withHeaders([
            'client_id' => $clientId,
            'sign' => $sign,
            't' => $timestamp,
            'sign_method' => 'HMAC-SHA256',
            'nonce' => '',
            'stringToSign' => '',
        ])->get($apiUrl);
        var_dump($response->json());
        // Periksa apakah permintaan berhasil
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['access_token'])) {
                
                
                return $data['access_token'];
            }
        }
    
        // Jika ada kesalahan, Anda dapat menangani kesalahan sesuai kebutuhan Anda
        return null;
    }

    // public function getAccessToken()
    // {



    //     $timestamp = time();
    //     $nonce = '';
    //     $url = $this->baseUrl. '/v1.0/token?grant_type=1';
    //     $httpMethod = 'GET';
    //     $query = '';
    //     $mode = 'formdata';
    //     $headers = [
    //         'client_id' => $this->clientId,
    //         'sign' => '', // Tidak perlu diisi karena akan dihitung
    //         't' => $timestamp,
    //         'sign_method' => 'HMAC-SHA256',
    //         'nonce' => $nonce,
    //         'stringToSign' => '', // Tidak perlu diisi karena akan dihitung
    //     ];
    
    //     // Hitung nilai 'sign' dan 'stringToSign' sesuai dengan permintaan cURL
    //     $signMap = $this->stringToSign($query, $mode, $httpMethod, $this->secret);
    //     $urlStr = $signMap['url'];
    //     $signStr = $signMap['signUrl'];
    //     $sign = $this->calcSign($this->clientId, $timestamp, $nonce, $signStr, $this->secret);
    
    //     // Set nilai 'sign' dan 'stringToSign' ke dalam header
    //     $headers['sign'] = $sign;
    //     $headers['stringToSign'] = $signStr;
    
    //     // Buat permintaan HTTP dengan header yang telah disiapkan
    //     $response = Http::withHeaders($headers)->get($url);
    
    //     // Lakukan apa yang perlu Anda lakukan dengan respons
    //     // Misalnya, Anda bisa mengembalikan atau memproses respons ini.

    //     // Buat permintaan HTTP dengan header yang telah disiapkan
    //     $response = Http::withHeaders($headers)->get($url);

    //     // Periksa apakah permintaan berhasil
    //     if ($response->successful()) {
    //         // Ambil data JSON dari respons dan kembalikan 'access_token'
    //         $data = $response->json();
    //         if (isset($data['access_token'])) {
                
    //             echo $data['access_token'];
    //             return $data['access_token'];
    //         }
    //     }

    //     // Jika permintaan tidak berhasil atau 'access_token' tidak ditemukan, Anda bisa mengembalikan nilai default atau mengambil tindakan yang sesuai.
    //     return null;


    //     // $timestamp = time();

    //     // // Buat signature sesuai kebutuhan Anda, Anda mungkin perlu mengimplementasinya
    //     // $signature = $this->createSignature(); 

    //     // $response = Http::get($this->baseUrl . '/v1.0/token', [
    //     //     'grant_type' => '1'
    //     // ], [
    //     //     'headers' => [
    //     //         'client_id' => $this->clientId,
    //     //         'sign' => $signature,
    //     //         't' => $timestamp,
    //     //         'sign_method' => 'HMAC-SHA256',
    //     //         'nonce' => '',
    //     //         'stringToSign' => ''
    //     //     ],
    //     // ]);

    //     // var_dump($response->json());
    //     // return $response->json();
        
    // }

    public function getDataPool()
    {
        $timestamp = time();

        // Buat signature sesuai kebutuhan Anda, Anda mungkin perlu mengimplementasinya
        $signature = $this->createSignature(); 

        $client = new Client();

        // Ambil data dari environment Postman
        $environment = json_decode(env('POSTMAN'), true);


        $apiUrl = $this->baseUrl;
        $clientId = $this->clientId;
        $secret = $this->secret;
        $device_id = $this->device_id;
        $timestamp = time();

        // // Buat tanda tangan (signature)
        // $signature = $this->generateSignature($clientId, $timestamp, $secret);

        // Buat permintaan GET ke API
        $response = $client->request('GET', $apiUrl . "/v1.0/devices/$device_id", [
            'headers' => [
                'client_id' => $clientId,
                'access_token' => $environment['values'][6]['value'],
                'sign' => $signature,
                't' => $timestamp,
                'sign_method' => 'HMAC-SHA256',
                'nonce' => '',
                'stringToSign' => '',
            ],
        ]);

        // $responseData = json_decode($response->getBody(), true);
        // var_dump($responseData);
        // return $response->json();
    }

    // store to db
    public function storeSensor(Request $request)
    {
        $validatedData = $request->validate([
            'temp_current' => 'required|numeric',
            'ph_current' => 'required|numeric',
            'tds_current' => 'required|numeric',
            'ec_current' => 'required|numeric',
            'salinity_current' => 'required|numeric',
        ]);

        $sensorData = SensorData::create($validatedData);

        return response()->json([
            'message' => 'Data successfully saved.',
            'data' => $sensorData,
        ], 201);
    }

    public function storeSensor2(Request $request)
    {
        $client = new Client();

        // Ambil data dari environment Postman
        $environment = json_decode(env('POSTMAN_ENVIRONMENT'), true);

        $apiUrl = $environment['values'][0]['value'];
        $clientId = $environment['values'][1]['value'];
        $secret = $environment['values'][2]['value'];
        $device_id = $environment['values'][3]['value'];
        $timestamp = time();

        // Buat tanda tangan (signature)
        $signature = $this->generateSignature($clientId, $timestamp, $secret);

        // Buat permintaan GET ke API
        $response = $client->request('GET', $apiUrl . "/v1.0/devices/$device_id", [
            'headers' => [
                'client_id' => $clientId,
                'access_token' => $environment['values'][6]['value'],
                'sign' => $signature,
                't' => $timestamp,
                'sign_method' => 'HMAC-SHA256',
                'nonce' => '',
                'stringToSign' => '',
            ],
        ]);

        $responseData = json_decode($response->getBody(), true);

        // Ambil data yang Anda perlukan dari respons API
        $temp_current = $responseData['temp_current'];
        $ph_current = $responseData['ph_current'];
        $tds_current = $responseData['tds_current'];
        $ec_current = $responseData['ec_current'];
        $salinity_current = $responseData['salinity_current'];

        // Simpan data ke database
        $sensorData = SensorData::create([
            'temp_current' => $temp_current,
            'ph_current' => $ph_current,
            'tds_current' => $tds_current,
            'ec_current' => $ec_current,
            'salinity_current' => $salinity_current,
        ]);

        return response()->json([
            'message' => 'Data successfully saved.',
            'data' => $sensorData,
        ], 201);
    }

    private function generateSignature($clientId, $timestamp, $secret)
    {
        $str = $clientId . $timestamp;
        $hash = hash_hmac('sha256', $str, $secret);
        return strtoupper($hash);
    }

    private function stringToSign($query, $mode, $httpMethod, $secret) {
        $sha256 = "";
        $url = "";
        $headersStr = "";
       
        // Hitung SHA256 dari data tertentu, contoh: bodyStr
        $sha256 = hash('sha256', 1);
    
        // Konstruksi URL
        $url = "/" . $this->request->url->host . $this->request->url->path . '?' . http_build_query($query);
    
        // Konstruksi string header jika diperlukan
        if ($this->request->headers->has("Signature-Headers") && $this->request->headers->get("Signature-Headers")) {
            $signHeaderStr = $this->request->headers->get("Signature-Headers");
            $signHeaderKeys = explode(":", $signHeaderStr);
    
            foreach ($signHeaderKeys as $item) {
                $val = "";
                if ($this->isSelected($jsonHeaders, $item) && $this->request->headers->has($item)) {
                    $val = $this->request->headers->get($item);
                }
                $headersStr .= $item . ":" . $val . "\n";
            }
        }
    
        // Konstruksi map untuk data yang akan digunakan dalam tanda tangan
        $map = [
            "signUrl" => $httpMethod . "\n" . $sha256 . "\n" . $headersStr . "\n" . $url,
            "url" => $url
        ];
    
        return $map;
    }
    // Buat metode ini sesuai kebutuhan Anda untuk menghasilkan signature
    private function createSignature()
    {
        // Implementasikan logika pembuatan signature sesuai dengan kode Postman
    }
}
