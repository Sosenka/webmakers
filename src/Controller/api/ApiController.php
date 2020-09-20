<?php


namespace App\Controller\api;


use App\Entity\Weather;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiController extends AbstractController
{
    private $client;
    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $weatherRepository;
    private $apiKey = '2782634eed1c90284914691197d5acc4';

    public function __construct(HttpClientInterface $client, EntityManagerInterface $em)
    {
        $this->client = $client;
        $this->em = $em;
        $this->weatherRepository = $this->em->getRepository(Weather::class);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws TransportExceptionInterface
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @Route("/api/get-weather", name="get_weather", methods={"POST", "GET"})
     */
    public function getAction(Request $request)
    {
        $data = $request->request->all();
        $response = $this->client->request('GET', 'https://api.openweathermap.org/data/2.5/weather?lat=&lon=&appid=', [
            'query' => [
                'lat' => $request->request->get('lat'),
                'lon' => $request->request->get('lng'),
                'appid' => $this->apiKey,
            ],
        ]);
        $response = $response->toArray();

         if($this->saveWeather($response)){
             return new JsonResponse(['status' => 'success', 'message' => 'Pogoda została zapisana'], 200);
         } else {
             return new JsonResponse(['status' => 'error', 'message' => 'Coś poszło nie tak!'], 500);
         }
    }

    public function saveWeather($response)
    {

        if ($response !== null){
            $weather = new Weather();

            $temp = $this->k_to_c($response['main']['temp']);
            $weather->setCity($response['name'])
                ->setLat($response['coord']['lat'])
                ->setLng($response['coord']['lon'])
                ->setWindSpeed($response['wind']['speed'])
                ->setVisibility($response['visibility'])
                ->setDescription($response['weather'][0]['description'])
                ->setMainWeather($response['weather'][0]['main'])
                ->setTemp($temp);
            $this->em->persist($weather);
            $this->em->flush();

            return true;
        }else {
            return false;
        }
    }

    private function k_to_c($temp) {
        if ( !is_numeric($temp) ) { return false; }
        return round(($temp - 273.15));
    }
}
