<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Work\Entity\WorkCategory;
use App\Domain\Work\Repository\WorkRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{

    public function __construct(private WorkRepository $workRepository)
    {
    }

    #[Route('/', name: 'index')]
    public function Index(): Response
    {
        $works = $this->workRepository->findCurrentWork();
        return $this->render('index.html.twig', [
            "works" => $works,
        ]);
    }

    #[Route('/_ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('ui.html.twig');
    }
}
