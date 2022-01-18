<?php

namespace App\Domain\User\Command;

use App\Domain\User\Entity\InternalUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddInternalUser extends Command
{
    protected static $defaultName = 'app:user:internal:add';

    public function __construct(
        private EntityManagerInterface $em,
    )
    {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $username = $io->ask('Username');
        $email = $io->ask('Email');
        $password = $io->askHidden('Mot de passe');

        $user = new InternalUser();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setPlainPassword($password);

        $this->em->persist($user);
        $this->em->flush();

        return Command::SUCCESS;
    }
}