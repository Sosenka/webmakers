<?php


namespace App\Controller\client;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ClientController extends AbstractController
{
    public function saveWeather(Request $request)
    {
        $data = $request->request->get('weather');

        return new Response($data);
    }
}
