<?php

namespace App\Http\Main\Controller;

use App\Infrastructure\Parameter\IParameterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planning')]
class PlanningController extends AbstractController
{

    public function __construct(private IParameterService $parameterService)
    {
    }

    #[Route('', name: 'planning_index')]
    public function Index(): Response
    {
        return $this->redirect($this->parameterService->getPlanningWebSite());
    }
}