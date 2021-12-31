<?php

declare(strict_types=1);

namespace App\Http\Security\Controller;

use App\Infrastructure\Discord\OauthDiscordUrl;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/login')]
class DiscordController extends AbstractController
{
    public function __construct(private OauthDiscordUrl $discordUrl)
    {
    }

    #[Route('', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $errors = $authenticationUtils->getLastAuthenticationError();

        return $this->render('security/login.html.twig', [
            'error' => $errors,
        ]);
    }

    #[Route('/discord', name: 'login_discord')]
    public function discord(): Response
    {
        return $this->redirect($this->discordUrl->getUrlLogin());
    }

    #[Route('/discord/check', name: 'login_discord_check')]
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
