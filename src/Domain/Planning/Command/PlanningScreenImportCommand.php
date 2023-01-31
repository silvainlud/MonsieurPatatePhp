<?php

declare(strict_types=1);

namespace App\Domain\Planning\Command;

use App\Domain\Planning\Entity\PlanningScreen;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpKernel\KernelInterface;

#[AsCommand("app:planning:screen:import")]
class PlanningScreenImportCommand extends Command
{
    public function __construct(
        private KernelInterface $kernel,
        private EntityManagerInterface $em,
        private LoggerInterface $appLogger
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countImage = 0;
        $filesystem = new Filesystem();
        if ($filesystem->exists($this->getDataDir())) {
            $finder = new Finder();
            $finder->name('planning_*.png')->files();
            foreach ($finder->in($this->getDataDir()) as $f) {
                $fileName = $f->getFilenameWithoutExtension();
                $split = explode('_', $fileName);
                $year = (int) $split[2];
                $week = (int) $split[1];

                $screen = $this->em->getRepository(PlanningScreen::class)->findOneBy(['year' => $year, 'week' => $week]);
                if ($screen === null) {
                    $screen = new PlanningScreen($week, $year);
                    $this->em->persist($screen);
                }
                $screen->setFile($f->getContents());

                $this->em->flush();
                ++$countImage;
            }
            if ($countImage > 0) {
                $this->em->flush();
            }

            foreach ($finder->in($this->getDataDir()) as $f) {
                $filesystem->remove((string) $f->getRealPath());
            }
        }

        $this->appLogger->info('Planning Screen : importation de screenshots ' . $countImage . ' de l\'ADE.');

        return Command::SUCCESS;
    }

    private function getDataDir(): string
    {
        return $this->kernel->getProjectDir() . '/var/data';
    }
}
