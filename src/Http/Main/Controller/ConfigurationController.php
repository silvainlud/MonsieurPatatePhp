<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Guild\Form\GuildSettingsType;
use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Planning\Entity\PlanningScreen;
use App\Domain\Section\Entity\Section;
use App\Domain\User\Entity\User;
use App\Domain\Work\Entity\WorkCategory;
use App\Domain\Work\Form\WorkCategoryType;
use App\Infrastructure\Discord\Entity\Channel\CategoryChannel;
use App\Infrastructure\Discord\Entity\DiscordRole;
use App\Infrastructure\Discord\IDiscordGuildService;
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
    public const QUERY_HIDDEN_CATEGORIES = 'display_hidden_categories';

    public function __construct(
        private IParameterService $parameterService,
        private EntityManagerInterface $em,
        private IDiscordGuildService $guildService,
    ) {
    }

    #[Route('', name: 'config')]
    public function discordWorkChannel(Request $request): Response
    {
        $guildSettings = $this->em->getRepository(GuildSettings::class)->find($this->parameterService->getGuildId());
        if ($guildSettings === null) {
            $guildSettings = new GuildSettings($this->parameterService->getGuildId());
        }

        if ((bool) $request->query->get(self::QUERY_HIDDEN_CATEGORIES, null) === true) {
            $categories = $this->em->getRepository(WorkCategory::class)->findBy([], ['name' => 'asc']);
        } else {
            $categories = $this->em->getRepository(WorkCategory::class)->findBy(['active' => true], ['name' => 'asc']);
        }

        $form = $this->createForm(GuildSettingsType::class, $guildSettings);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($guildSettings);
            $this->em->flush();

            return $this->redirectToRoute('config');
        }

        return $this->render('config/workChannel.html.twig', [
            'form' => $form->createView(),
            'categories' => $categories,
        ]);
    }

    #[Route('work-category/add', name: 'work_category_add')]
    #[Route('work-category/edit/{category}', name: 'work_category_edit')]
    public function modifyWorkCategory(Request $request, ?WorkCategory $category = null): Response
    {
        if ($category === null) {
            $category = new WorkCategory();
        }
        $form = $this->createForm(WorkCategoryType::class, $category);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($category);
            $this->em->flush();

            return $this->redirectToRoute('config');
        }

        return $this->render('config/work_category/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('db', name: 'config_database')]
    public function database(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $users = $this->em->getRepository(User::class)->findAll();
        $sections = $this->em->getRepository(Section::class)->findBy(['guildSettings' => $this->parameterService->getGuildId()]);
        $screens = $this->em->getRepository(PlanningScreen::class)->findBy([], ['year' => 'desc', 'week' => 'desc']);
        $itemLogs = $this->em->getRepository(PlanningLog::class)->findBy([], ['dateCreate' => 'desc']);

        $channels = $this->guildService->getChannels((int) $this->parameterService->getGuildId());
        $categoryChannels = [];
        $otherChannels = [];
        foreach ($channels as $c) {
            if ($c instanceof CategoryChannel) {
                $categoryChannels[$c->getId()] = $c;
                foreach ($c->getChannels() as $o) {
                    $otherChannels[$o->getId()] = $o;
                }
            } else {
                $otherChannels[$c->getId()] = $c;
            }
        }

        $roles = $this->guildService->getRoles();
        $roles = array_reduce($roles, function (array $acc, DiscordRole $role) {
            $acc[$role->getId()] = $role;

            return $acc;
        }, []);

        return $this->render('config/database/index.html.twig', [
            'users' => $users,
            'sections' => $sections,
            'screens' => $screens,
            'itemLogs' => $itemLogs,
            'categories' => $categoryChannels,
            'channels' => $otherChannels,
            'roles' => $roles,
        ]);
    }
}
