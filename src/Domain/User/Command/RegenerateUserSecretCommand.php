<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\User\Entity\DiscordUser;
use App\Domain\User\UserSecretGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand("app:user:regenerate")]
class RegenerateUserSecretCommand extends Command
{

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getRepository(DiscordUser::class)->findAll();
        foreach ($users as $u) {
            $u->setSecretKey(UserSecretGenerator::generateSecret());
        }
        $this->em->flush();

        return Command::SUCCESS;
    }
}
