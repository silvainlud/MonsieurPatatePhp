<?php

declare(strict_types=1);

namespace App\Domain\Work\Command;

use App\Domain\Work\IWorkDiscordNotifyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class WorkRecallCommand extends Command
{
    protected static $defaultName = 'app:work:recall';

    public function __construct(
        private IWorkDiscordNotifyService $notifyService,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $works = $this->notifyService->processRecall();

        $io->success('Lancement de ' . \count($works) . ' rappel(s).');

        return Command::SUCCESS;
    }

    protected function configure(): void
    {
        $this->setDescription('Lancement des rappel concernant les devoirs.');
    }
}
