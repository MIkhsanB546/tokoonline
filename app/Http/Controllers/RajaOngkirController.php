<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller
{
    public function getProvinces()
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/province');

        $data = $response->json();

        return response()->json($data['data']);
    }
    public function getCities(Request $request)
    {
        $provinceId = $request->province_id;

        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/city/' . $provinceId);

        $data = $response->json();

        return response()->json($data['data']);
    }
    public function getDistricts($cityId)
    {
        $response = Http::timeout(30)
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->get(env('RAJAONGKIR_BASE_URL') . '/destination/district/' . $cityId);

        $data = $response->json();

        return response()->json($data['data']);
    }
    public function getCost(Request $request)
    {
        $response = Http::timeout(30)
            ->asForm()
            ->withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])
            ->post(env('RAJAONGKIR_BASE_URL') . '/calculate/domestic-cost', [

                'origin' => $request->origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier,
                'price' => 'lowest'
            ]);

        $data = $response->json();

        return response()->json($data['data']);
    }
}
