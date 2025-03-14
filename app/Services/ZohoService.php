<?php

namespace App\Services;

use App\Models\ZohoToken;
use Illuminate\Support\Facades\Http;

class ZohoService
{

    protected string $apiUrl;
    private string $accessToken;


    /**
     * @throws \Exception
     */
    public function __construct()
    {

        $this->apiUrl = config('services.zoho.zoho_api_url');
        $this->accessToken = $this->getAccessToken();
    }

    /**
     * @throws \Exception
     */
    private function getAccessToken()
    {
        $token = ZohoToken::query()->latest()->first();

        if ($token && now()->lte($token->expires_at)) {
            return $token->access_token;
        }

        $response = Http::asForm()->post('https://accounts.zoho.com/oauth/v2/token', [
            'refresh_token' => config('services.zoho.refresh_token'),
            'client_id' => config('services.zoho.client_id'),
            'client_secret' => config('services.zoho.client_secret'),
            'grant_type' => 'refresh_token',
        ]);


        if ($response->failed()) {
            throw new \Exception('Failed to refresh Zoho access token');
        }


        $data = $response->json();

        ZohoToken::query()->updateOrCreate(['expires_at' => ZohoToken::query()->max('expires_at')], [
            'access_token' => $data['access_token'],
            'expires_at' => now()->addSeconds(3600),
        ]);

        return $data['access_token'];
    }


    public function createAccount(array $data)
    {
        $url = "$this->apiUrl/crm/v2/Accounts";

        $response = Http::withToken($this->accessToken)->post($url, [
            'data' => $data
        ]);

        return $response->json();

    }

    public function createDeal(array $data)
    {
        $url = "$this->apiUrl/crm/v2/deals";

        $response = Http::withToken($this->accessToken)->post($url, [
            'data' => $data
        ]);

        return $response->json();
    }





}
