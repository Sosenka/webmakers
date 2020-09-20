<?php


namespace App\Controller\client;


use App\Entity\Weather;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HistoryController extends AbstractController
{

    /**
     * @var EntityManagerInterface
     */
    private $em;
    private $weatherRepository;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->weatherRepository = $this->em->getRepository(Weather::class);
    }

    public function showHistory(Request $request)
    {
        $topTemp = $this->weatherRepository->showBestTemp();

        return $this->render('history.html.twig', [
            'topTemp' => $topTemp
    ]);

    }
}
