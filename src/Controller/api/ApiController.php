<?php


namespace App\Controller\api;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    private $client;
    private $apiKey = '2782634eed1c90284914691197d5acc4';

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Request $request
     * @return \App\Controller\api\JsonResponse
     * @throws TransportExceptionInterface
     * @Route("/api/get-weather", name="get_weather", methods={"POST", "GET"})
     */
    public function getAction(Request $request)
    {
        $data = $request->request->all();
        $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/weather?lat=&lon=&appid=', [
            'query' => [
                'lat' => $request->request->get('lat'),
                'lon' => $request->request->get('lng'),
                'appid' => '2782634eed1c90284914691197d5acc4',
            ],
        ]);
        $response = $response->toArray();

         $this->saveWeather($response);

        return new JsonResponse(['status' => 'success', 'message' => 'Pogoda została zapisana'], 200);
    }

    public function saveWeather($response)
    {
//        $this->k_to_c($response['temp']);
    }

    private function k_to_c($temp) {
        if ( !is_numeric($temp) ) { return false; }
        return round(($temp - 273.15));
    }
}
