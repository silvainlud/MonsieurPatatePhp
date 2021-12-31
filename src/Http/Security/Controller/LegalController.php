<?php

declare(strict_types=1);

namespace App\Http\Security\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/legal')]
class LegalController extends AbstractController
{
    #[Route('/notice', name: 'legal_notice')]
    public function legalNotice(): Response
    {
        return $this->render('security/legal/notice.html.twig', [
        ]);
    }
}
