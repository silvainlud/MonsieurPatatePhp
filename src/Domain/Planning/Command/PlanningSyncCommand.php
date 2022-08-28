<?php

declare(strict_types=1);

namespace App\Domain\Planning\Command;

use App\Domain\Planning\Entity\PlanningItem;
use App\Domain\Planning\Entity\PlanningLog;
use App\Domain\Planning\IPlanningDiscordNotifyService;
use App\Domain\Planning\IPlanningSynchronizeService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class PlanningSyncCommand extends Command
{
    protected static $defaultName = 'app:planning:sync';

    public function __construct(
        private EntityManagerInterface $em,
        private IPlanningSynchronizeService $planningConverterService,
        private IPlanningDiscordNotifyService $discordMessageService,
        private LoggerInterface $appLogger,
    ) {
        parent::__construct(self::$defaultName);
    }

    public function purge(): void
    {
        foreach ($this->em->getRepository(PlanningItem::class)->findAll() as $item) {
            $this->em->remove($item);
        }
        foreach ($this->em->getRepository(PlanningLog::class)->findAll() as $item) {
            $this->em->remove($item);
        }
        $this->em->flush();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $initMode = (bool) $input->getOption('init');
        $purgeMode = (bool) $input->getOption('purge');

        if ($purgeMode) {
            $question = $io->confirm('Purge?', false);

            if ($question === false) {
                return Command::SUCCESS;
            }
            $io->caution('La base de données a été vidé.');
            $this->purge();
        }

        $previousLog = [];
        if ($initMode) {
            $previousLog = $this->em->getRepository(PlanningLog::class)->findBy(['isDiscordSend' => false]);
        }

        $data = $this->planningConverterService->reload();

        if ($initMode) {
            $previousLogUuid = array_map(fn (PlanningLog $l) => $l->getId(), $previousLog);
            $previousLog = $this->em->getRepository(PlanningLog::class)->findBy(['isDiscordSend' => false]);

            /** @var PlanningLog $l */
            foreach ($previousLog as $l) {
                if (!\in_array($l->getId(), $previousLogUuid, true) && !$l->isDiscordSend()) {
                    $l->setIsDiscordSend(true);
                }
            }
            $this->em->flush();
        }

        $io->success('Importation de ' . \count($data) . " éléments de l'ADE.");
        $this->appLogger->info('Planning Sync : Importation de ' . \count($data) . " éléments de l'ADE dans la DB.");

        $this->discordMessageService->notifyLogs();

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this
            ->setDescription("Synchronisation de l'emplois du temps.")
            ->addOption('init', null, InputOption::VALUE_NONE, 'En mode initialisation (pas de notifications discord).')
            ->addOption('purge', null, InputOption::VALUE_NONE, 'Vider la base de la base de données.');
    }
}
