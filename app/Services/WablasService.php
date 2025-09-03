<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WablasService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('WABLAS_API_KEY');
        $this->baseUrl = env('WABLAS_BASE_URL', 'https://texas.wablas.com');
    }

    public function sendMessage(string $phone, string $message): array
    {
        $url = $this->baseUrl . '/api/send-message';

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
        ])->post($url, [
            'phone' => $phone,
            'message' => $message,
        ]);

        return [
            'success' => $response->successful(),
            'data' => $response->json()
        ];
    }

    public function sendBulkMessage(array $phoneNumbers, string $message): array
    {
        $url = $this->baseUrl . '/api/v2/send-message';

        $payload = [
            'data' => collect($phoneNumbers)->map(function ($number) use ($message) {
                return [
                    'phone' => $number,
                    'message' => $message,
                    'isGroup' => 'false',
                ];
            })->values()->all(),
        ];

        $response = Http::withHeaders([
            'Authorization' => $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($url, $payload);

        return [
            'success' => $response->successful(),
            'data' => $response->json(),
        ];
    }

}
