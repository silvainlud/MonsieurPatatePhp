<?php

declare(strict_types=1);

namespace App\Domain\Planning;

use App\Domain\Planning\Entity\PlanningItem;
use App\Domain\Planning\Entity\PlanningLog;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use ICal\ICal;
use JetBrains\PhpStorm\NoReturn;

class PlanningSynchronizeService implements IPlanningSynchronizeService
{
    public function __construct(
        private string $calendarUrl,
        private EntityManagerInterface $em
    ) {
    }

    public function getCalendarFile(): string
    {
        return (string) file_get_contents($this->calendarUrl, context: stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]));
    }

    #[NoReturn]
    public function reload(): array
    {
        $items = array_filter($this->loadPlanning(), [$this, 'filter']);

        foreach ($items as $i) {
            $this->sync_exist($i);
        }

        $this->purge($items);
        $this->em->flush();

        return $items;
    }

    /** @return PlanningItem[] */
    public function loadPlanning(): array
    {
        $events = (new ICal($this->getCalendarFile()))->events();
        $data = [];
        foreach ($events as $e) {
            $i = (new PlanningItem($e->uid, new DateTime($e->created)))
                ->setTitle($e->summary)
                ->setDateStart(self::RemakeDate($e->dtstart))
                ->setDateEnd(self::RemakeDate($e->dtend))
                ->setDateModified($e->last_modified === null ? null : self::RemakeDate($e->last_modified))
                ->setLocation($e->location);

            $desc = explode("\n", $e->description);
            if (\count($desc) >= 2) {
                $i->setTeacher($desc[\count($desc) - 2]);
                $i->setDescription(implode("\n", array_splice($desc, 0, \count($desc) - 2)));
            } else {
                $i->setDescription($e->description);
            }

            $data[] = $i;
        }

        return $data;
    }

    public function filter(PlanningItem $i): bool
    {
        return $i->getDateEnd() >= (new DateTime()) && (
            !str_contains('vacance', strtolower($i->getTitle()))
                && !str_contains('vacances', strtolower($i->getTitle()))
        );
    }

    public function sync_exist(PlanningItem $item): ?PlanningLog
    {
        /** @var ?PlanningItem $previous */
        $previous = $this->em->getRepository(PlanningItem::class)->find($item->getId());
        if ($previous !== null) {
            if (PlanningLog::isDiff($previous, $item)) {
                $log = new PlanningLog($previous, $item);
                $previous->update($item);
                $this->em->persist($log);

                return $log;
            }

            return null;
        }
        $log = new PlanningLog($previous, $item);
        $this->em->persist($item);
        $this->em->persist($log);

        return $log;
    }

    /** @param PlanningItem[] $items */
    public function purge(array $items): void
    {
        $uuids = array_map(fn (PlanningItem $i) => $i->getId(), $items);
        $previous = $this->em->getRepository(PlanningItem::class)->findByUuidNotIn($uuids);
        foreach ($previous as $p) {
            if ($this->filter($p)) {
                $log = new PlanningLog($p, null);
                $this->em->persist($log);
            }
            $this->em->remove($p);
        }
    }

    private static function RemakeDate(string $date): DateTime
    {
        $paris = new DateTimeZone('Europe/Paris');

        return new DateTime((new DateTime($date, new DateTimeZone('UTC')))->setTimezone($paris)->format('Y-m-d H:i'));
    }
}
