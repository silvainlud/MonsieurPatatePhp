<?php

namespace App\Domain\Planning;

use App\Domain\Planning\Entity\PlanningItem;
use App\Domain\Planning\Entity\PlanningLog;
use App\Infrastructure\Discord\IDiscordGuildService;
use App\Infrastructure\Parameter\IParameterService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Environment;
use Twig\Extra\Intl\IntlExtension;

class PlanningDiscordNotifyService implements IPlanningDiscordNotifyService
{


    public const HEADER_TITLE = ":information_source: Nom";
    public const HEADER_DATE_START = ":clock2: Début";
    public const HEADER_DATE_END = ":clock3: Fin";
    public const HEADER_TEACHER = ":teacher: Professeur";
    public const HEADER_LOCATION = ":map: Salle";

    public function __construct(
        private IParameterService      $parameterService,
        private EntityManagerInterface $em,
        private HttpClientInterface    $discordClient,
        private IntlExtension          $intlExtension,
        private IDiscordGuildService   $guildService,
        private Environment            $twig
    )
    {
    }

    public function notifyLogs(): void
    {
        $logs = $this->em->getRepository(PlanningLog::class)->findAll();
        usort($logs, function (PlanningLog $a, PlanningLog $b) {
            $aDate = $a->getDateStartNext() ?? $a->getDateStartPrevious();
            $bDate = $b->getDateStartNext() ?? $b->getDateStartPrevious();
            return $aDate == $bDate ? 0 : ($aDate > $bDate ? 1 : -1);
        });

        foreach ($logs as $log) {
            $this->notify($log);
        }
        $this->em->flush();
    }

    public function notify(PlanningLog $log, bool $retry = true): void
    {
        if ($log->isDiscordSend()) {
            if ($log->getDateCreate() < new \DateTime("- 1 months")) {
                $item = $this->em->getRepository(PlanningItem::class)->find($log->getPlanningUuid());
                if ($item === null)
                    $this->em->remove($log);
            }
            return;
        }

        $title = "";
        $fields = "";

        switch ($log->getActionType()) {
            case PlanningLog::ACTION_TYPE_ADD:
                $title .= ":new: AJOUT : " . $log->getTitleNext();
                $fields .= "__" . self::HEADER_DATE_START . "__ : " . $this->formateDate($log->getDateStartNext()) . "\n";
                $fields .= "__" . self::HEADER_DATE_END . "__ : " . $this->formateDate($log->getDateEndNext()) . "\n";
                $fields .= "__" . self::HEADER_TEACHER . "__ : " . $log->getTeacherNext() . "\n";
                $fields .= "__" . self::HEADER_LOCATION . "__ : " . $log->getLocationNext() . "\n";
                break;
            case PlanningLog::ACTION_TYPE_DELETE:
                $title .= ":x: SUPPRESSION : " . $log->getTitlePrevious();
                $fields .= "__" . self::HEADER_DATE_START . "__ : " . $this->formateDate($log->getDateStartPrevious()) . "\n";
                $fields .= "__" . self::HEADER_DATE_END . "__ : " . $this->formateDate($log->getDateEndPrevious()) . "\n";
                $fields .= "__" . self::HEADER_TEACHER . "__ : " . $log->getTeacherPrevious() . "\n";
                $fields .= "__" . self::HEADER_LOCATION . "__ : " . $log->getLocationPrevious() . "\n";
                break;
            case PlanningLog::ACTION_TYPE_UPDATE:
                $title .= ":arrows_counterclockwise: UPDATE : " . $log->getTitlePrevious();
                if (in_array(PlanningLog::FIELD_TITLE, $log->getUpdatedField()))
                    $fields .= "__" . self::HEADER_TITLE . "__ : ~~" . $log->getTitlePrevious() . "~~ " . $log->getTitleNext() . " \n";

                if (in_array(PlanningLog::FIELD_DATE_START, $log->getUpdatedField()))
                    $fields .= "__" . self::HEADER_DATE_START . "__ : ~~" . $this->formateDate($log->getDateStartPrevious()) . "~~ " . $this->formateDate($log->getDateStartNext()) . " \n";
                else
                    $fields .= "__" . self::HEADER_DATE_START . "__ : " . $this->formateDate($log->getDateStartNext()) . "\n";

                if (in_array(PlanningLog::FIELD_DATE_END, $log->getUpdatedField()))
                    $fields .= "__" . self::HEADER_DATE_END . "__ : ~~" . $this->formateDate($log->getDateEndPrevious()) . "~~ " . $this->formateDate($log->getDateEndNext()) . " \n";
                else
                    $fields .= "__" . self::HEADER_DATE_END . "__ : " . $this->formateDate($log->getDateEndNext()) . "\n";

                if (in_array(PlanningLog::FIELD_TEACHER, $log->getUpdatedField()))
                    $fields .= "__" . self::HEADER_TEACHER . "__ : ~~" . $log->getTeacherPrevious() . "~~ " . $log->getTeacherNext() . " \n";
                else
                    $fields .= "__" . self::HEADER_TEACHER . "__ : " . $log->getTeacherNext() . "\n";

                if (in_array(PlanningLog::FIELD_LOCATION, $log->getUpdatedField()))
                    $fields .= "__" . self::HEADER_LOCATION . "__ : ~~" . $log->getLocationPrevious() . "~~ " . $log->getLocationNext() . " \n";
                else
                    $fields .= "__" . self::HEADER_LOCATION . "__ : " . $log->getLocationNext() . "\n";

                break;
        }

        $resp = $this->discordClient->request(Request::METHOD_POST, 'channels/794344786890719304/messages', ["json" => [
            "embeds" => [
                [
                    "title" => $title,
                    "description" => $fields,
                    "author" => [
                        "name" => "ADE - Emplois du temps",
                        "url" => $this->parameterService->getPlanningWebSite(),
                        "icon_url" => $this->guildService->getCurrentGuildIcon(),
                    ],
                ]
            ],

        ]]);
        if ($resp->getStatusCode() === Response::HTTP_OK)
            $log->setIsDiscordSend(true);
        else {
            $res = json_decode($resp->getContent(false));
            if ($res->message === "You are being rate limited.") {
                if ($retry) {
                    sleep((int)round((int)$res->retry_after / 1000 + 1, mode: PHP_ROUND_HALF_UP));
                    $this->notify($log, false);
                }
            }
        }
    }

    private function formateDate(?\DateTime $date): string
    {
        if ($date === null)
            return  "???";
        return (string)$this->intlExtension->formatDateTime($this->twig, $date, pattern: "eeee d MMM HH'h'mm", locale: 'fr');
    }
}