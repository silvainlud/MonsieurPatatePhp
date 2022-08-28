<?php

declare(strict_types=1);

namespace App\Domain\Work\Command;

use App\Domain\Work\IWorkProcessNotifyService;
use App\Domain\Work\IWorkService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WorkRecallCommand extends Command
{
    protected static $defaultName = 'app:work:recall';

    public function __construct(
        private readonly IWorkProcessNotifyService $notifyService,
        private readonly IWorkService $workService,
        private readonly LoggerInterface $appLogger
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $allMode = (bool) $input->getOption('all');

        if ($allMode) {
            $this->workService->resetAllRecallDate();
        }

        $works = $this->notifyService->processRecall();

        $io->success('Lancement de ' . \count($works) . ' rappel(s).');
        $this->appLogger->info('Work Recall : Lancement de ' . \count($works) . ' rappel(s).');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('Lancement des rappel concernant les devoirs.')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Fixer toutes les dates de rappel de pour les devoirs.');
    }
}
