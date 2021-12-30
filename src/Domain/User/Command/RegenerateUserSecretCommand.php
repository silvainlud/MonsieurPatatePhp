<?php

declare(strict_types=1);

namespace App\Domain\User\Command;

use App\Domain\User\Entity\User;
use App\Domain\User\UserSecretGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegenerateUserSecretCommand extends Command
{
    protected static $defaultName = 'app:user:regenerate';

    public function __construct(
        private EntityManagerInterface $em,
    ) {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $users = $this->em->getRepository(User::class)->findAll();
        foreach ($users as $u) {
            $u->setSecretKey(UserSecretGenerator::generateSecret());
        }
        $this->em->flush();

        return Command::SUCCESS;
    }
}
