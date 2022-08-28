<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\Planning\Entity\PlanningItem;
use App\Domain\Planning\Entity\PlanningScreen;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/planning')]
class PlanningController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    #[Route('', name: 'planning_index')]
    #[Route('/{year<\d+>}/{week<\d+>}', name: 'planning_day')]
    public function Index(?int $year = null, ?int $week = null): Response
    {
        if ($week === null || $year === null) {
            $current = new \DateTime();
            $week = (int) $current->format('W');
            $year = (int) $current->format('o');
            [, $end] = $this->getStartAndEndDate($week, $year);
            $items = $this->em->getRepository(PlanningItem::class)->findBetweenDates(
                $current,
                $end->format('Y-m-d 23:59:59')
            );

            if (\count($items) === 0) {
                $current = $current->modify('+ 7 days');
                $week = (int) $current->format('W');
                $year = (int) $current->format('o');
            }
        }

        [$start, $end] = $this->getStartAndEndDate($week, $year);

        $screen = $this->em->getRepository(PlanningScreen::class)->findOneBy([
            'year' => $year,
            'week' => $week,
        ]);

        return $this->render('planning/index.html.twig', [
            'screen' => $screen,
            'start' => $start,
            'end' => $end,
            'next' => (clone $start)->modify('+1 weeks'),
            'previous' => (clone $start)->modify('-1 weeks'),
        ]);
    }

    #[Route('/screen/{year<\d+>}/{week<\d+>}/screen', name: 'planning_screen')]
    #[ParamConverter('screen', class: PlanningScreen::class)]
    public function Screen(PlanningScreen $screen): Response
    {
        $response = new Response();
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_INLINE,
            md5($screen->getWeek() . '_' . $screen->getYear()) . '.png'
        );
        $response->headers->set('Content-Disposition', $disposition);
        $response->headers->set('Content-Type', 'image/png');
        $response->setContent((string) stream_get_contents($screen->getFile()));

        return $response;
    }

    /** @return DateTime[] */
    private function getStartAndEndDate(int $week, int $year): array
    {
        $dto = new DateTime();
        $dto->setISODate($year, $week);
        $ret[0] = clone $dto;
        $dto->modify('+6 days');
        $ret[1] = $dto;

        return $ret;
    }
}
