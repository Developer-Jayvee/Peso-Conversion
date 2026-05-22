<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class CurrencyRateAPIService
{
    protected string $baseURI;
    public function __construct(string $baseURI , string $queryParams = 'from=PHP&to=USD')
    {
        $this->baseURI = $baseURI . "?" . $queryParams;
    }
    public function handler()
    {
        return Cache::remember('currency' , 50000 , function (){
            $response = Http::get($this->baseURI);
            return $response->json()['rates'] ?? 0;
        });
    }
}
