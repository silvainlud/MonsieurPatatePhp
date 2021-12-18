<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    #[Route("/connect/discord", name: "connect_discord_start")]
    public function connectAction(ClientRegistry $clientRegistry) : Response
    {
        return $this->redirect("https://discord.com/api/oauth2/authorize?client_id=876096685892325376&redirect_uri=http%3A%2F%2Flocalhost%3A8000%2Fconnect%2Fdiscord%2Fcheck&response_type=code&scope=identify%20email");
    }

    #[Route("/connect/discord/check", name: "connect_discord_check")]
    public function connectCheckAction(Request $request, ClientRegistry $clientRegistry) : Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }


    #[Route("/logout", name: "logout")]
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
