<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Work\Entity\Work;
use App\Domain\Work\Form\WorkType;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/work')]
class WorkController extends AbstractController
{
    public function __construct(
        private IParameterService $parameterService,
        private EntityManagerInterface $em
    ) {
    }

    #[Route('/add', name: 'work_add')]
    public function modify(Request $request, ?Work $work): Response
    {
        if ($work === null) {
            $work = new Work();
            $guild = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
            if ($guild === null) {
                throw $this->createNotFoundException();
            }
            $work->setGuild($guild);
        } elseif ($work->getGuild()->getServerId() !== $this->parameterService->getGuildId()) {
            throw $this->createNotFoundException();
        }

        $form = $this->createForm(WorkType::class, $work);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($work);
            $this->em->flush();

            return $this->redirectToRoute('index');
        }

        return $this->render('work/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'work_edit')]
    #[ParamConverter('work', class: Work::class)]
    public function edit(Work $work, Request $request): Response
    {
        return $this->modify($request, $work);
    }
}
