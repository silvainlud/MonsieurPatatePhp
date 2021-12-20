<?php

namespace App\Http\Main\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted("ROLE_ADMIN_DISCORD")]
#[Route("/config/")]
class ConfigurationController extends AbstractController
{
    public function __construct()
    {
    }

    #[Route("work-channel", name: "config_work_channel")]
    public function discordWorkChannel(): Response
    {
        return $this->render("base.html.twig");
    }
}