<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\OauthDiscordUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    public function __construct(private OauthDiscordUrl $discordUrl)
    {
    }

    #[Route('/login', name: 'login')]
    public function login(): Response
    {
        return $this->render('security/base.html.twig');
    }

    #[Route('/login/discord', name: 'login_discord')]
    public function discord(): Response
    {
        return $this->redirect($this->discordUrl->getUrlLogin());
    }

    #[Route('/login/discord/check', name: 'login_discord_check')]
    public function check(Request $request): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/logout', name: 'logout')]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
