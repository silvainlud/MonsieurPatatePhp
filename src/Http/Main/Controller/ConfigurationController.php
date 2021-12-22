<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Guild\Form\GuildSettingsType;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN_DISCORD')]
#[Route('/config/')]
class ConfigurationController extends AbstractController
{
    public function __construct(
        private IParameterService $parameterService,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('work-channel', name: 'config_work_channel')]
    public function discordWorkChannel(Request $request): Response
    {
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSettings === null) {
            $guildSettings = new GuildSettings($this->parameterService->getGuildId());
        }

        $form = $this->createForm(GuildSettingsType::class, $guildSettings);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($guildSettings);
            $this->em->flush();

            return $this->redirectToRoute('config_work_channel');
        }

        return $this->render('config/workChannel.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
