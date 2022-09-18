<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Guild\IGuildSettingsService;
use App\Domain\Planning\Repository\PlanningItemRepository;
use App\Domain\Work\Repository\WorkRepository;
use App\Infrastructure\Discord\Entity\Channel\Message\DiscordMessage;
use App\Infrastructure\Discord\IDiscordMessageService;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    public function __construct(
        private readonly WorkRepository $workRepository,
        private readonly PlanningItemRepository $planningItemRepository,
        private readonly IGuildSettingsService $guildSettingsService,
        private readonly IDiscordMessageService $discordMessageService,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    #[Route('/', name: 'index')]
    public function Index(): Response
    {
        $setting = $this->guildSettingsService->getCurrentSettings();

        $item = $this->cache->getItem('index_last_work_announce_msg');
        if (!$item->isHit()) {
            $item->set($this->discordMessageService->getLastMessage($setting->getWorkAnnounceChannelId() ?? ''));
            $item->expiresAfter(900);
            $this->cache->save($item);
        }

        /** @var ?DiscordMessage $lastMessage */
        $lastMessage = $item->get();

        $works = $this->workRepository->findCurrentWork();
        $items = $this->planningItemRepository->findFuture();
        $todayItems = $this->planningItemRepository->findDate();

        return $this->render('index.html.twig', [
            'works' => $works,
            'items' => $items,
            'todayItems' => $todayItems,
            'lastMessage' => $lastMessage,
        ]);
    }

    #[Route('/_ui', name: 'ui')]
    public function ui(): Response
    {
        return $this->render('ui.html.twig');
    }
}
