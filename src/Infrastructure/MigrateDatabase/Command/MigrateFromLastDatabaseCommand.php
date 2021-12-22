<?php

declare(strict_types=1);

namespace App\Infrastructure\MigrateDatabase\Command;

use App\Domain\Guild\Entity\GuildSettings;
use App\Domain\Section\Entity\RoleAllowSection;
use App\Domain\Section\Entity\Section;
use Doctrine\ORM\EntityManagerInterface;
use PDO;
use PHPUnit\Util\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateFromLastDatabaseCommand extends Command
{
    protected static $defaultName = 'app:migrate:from';

    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct(self::$defaultName);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $url = __DIR__ . '/../../../../potatoes.db';

            $pdo = new PDO('sqlite:' . $url);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare('select * from AnnounceChannel;');
            $stmt->execute();

            $data = $stmt->fetchAll();
            if ($data !== false) {
                foreach ($data as $v) {
                    $gs = new GuildSettings($v['GuidId']);
                    $gs->setAnnounceChannelId($v['AnnounceChannelId']);
                    $gs->setSectionMessageId($v['GameMessageId']);

                    $this->em->persist($gs);

                    $stmt = $pdo->prepare('select * from Game where guildId = ?;');
                    $stmt->execute([$gs->getServerId()]);
                    $sections = $stmt->fetchAll();

                    if ($sections !== false) {
                        foreach ($sections as $s) {
                            $ns = new Section();
                            $ns->setName($s['name'])
                                ->setGuildSettings($gs)
                                ->setCategoryId($s['categoryId'])
                                ->setCreatorId($s['memberIdCreate'])
                                ->setDate(new \DateTime(str_replace('_', ' ', $s['dateCreate'])))
                                ->setRoleId($s['roleId'])
                                ->setCreatorName($s['memberUsernameCreate'])
                                ->setEmoji(($s['emoticon']))
                                ->setVisibility($s['show'] ? 'public' : ($s['restricted'] ? 'private' : 'PROTECTED'))
                                ->setAnnounceChannelId($s['AnnounceChannelId']);

                            $gs->addSection($ns);

                            $stmt = $pdo->prepare('select * from GameRoleAllow where GameId = ?;');
                            $stmt->execute([$s['id']]);
                            $roles = $stmt->fetchAll();

                            if ($roles !== false) {
                                foreach ($roles as $r) {
                                    $nr = new RoleAllowSection();
                                    $nr->setRoleId($r['RoleId']);
                                    $nr->setSection($ns);

                                    $ns->addAllowRole($nr);
                                }
                            }
                        }
                    }
                }
                $this->em->flush();
            }
        } catch (Exception $e) {
            $output->writeln("Impossible d'accéder à la base de données SQLite : " . $e->getMessage());

            exit();
        }

        return Command::SUCCESS;
    }
}
