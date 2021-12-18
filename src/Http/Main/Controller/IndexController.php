<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function Index(): Response
    {
        return $this->render('base.html.twig');
    }
}
