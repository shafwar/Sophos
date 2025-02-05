<?php
// app/Http/Controllers/Api/AlertApiController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class AlertApiController extends Controller
{
    public function getAlerts(Request $request)
    {
        // Mengambil URL dan API Key dari file .env
        $url = env('https://api-us01.central.sophos.com/common/v1/alerts'); // Mengambil URL API
        $apiKey = env('SOPHOS_API_KEY'); // Mengambil API Key
        $authType = env('SOPHOS_API_AUTH_TYPE'); // Mengambil jenis autentikasi (misalnya Bearer)

        // Membuat instance Guzzle HTTP client
        $client = new Client();

        try {
            // Kirim request GET ke API Postman
            $response = $client->request('GET', $url, [
                'headers' => [
                    'Authorization' => $authType . ' ' . $apiKey,  // Menggunakan Bearer Token atau sesuai autentikasi
                ]
            ]);

            // Mendapatkan body response
            $data = json_decode($response->getBody(), true);

            // Mengembalikan response JSON
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
