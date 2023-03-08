<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class CallApiExtService
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function getData(string $city): array
    {   
        $datas = array();
        try {
            $response = $this->client->request(
                'GET',
                'https://api.openweathermap.org/data/2.5/weather?q='.$city.'&appid=ce7418650c86eae6629dfcfdda141c14&units=metric'
            );
            $datas = $response->toArray();
        } catch (\Exception $e) {
            dump("Probleme api");
        }
       
        //  return new JsonResponse($response->getContent(), $response->getStatusCode(), [], true);
        return $datas;
    }
}