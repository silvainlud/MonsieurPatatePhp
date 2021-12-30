<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Planning\Entity\PlanningScreen;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planning')]
class PlanningController extends AbstractController
{
    public function __construct(
        private IParameterService $parameterService,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('', name: 'planning_index')]
    public function Index(): Response
    {
        $current = new \DateTime();
        $screen = $this->em->getRepository(PlanningScreen::class)->findOneBy([
            'year' => $current->format('Y'),
            'week' => $current->format('W'),
        ]);

        if ($screen === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('planning/index.html.twig', [
            'screen' => $screen,
        ]);
    }

    #[Route('/screen/{year<\d+>}/{week<\d+>}', name: 'planning_screen')]
    #[ParamConverter('screen', class: PlanningScreen::class)]
    public function Screen(PlanningScreen $screen): Response
    {
        $response = new Response();
        $disposition = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_INLINE, md5(uniqid()));
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent((string) stream_get_contents($screen->getFile()));

        return $response;
    }
}
