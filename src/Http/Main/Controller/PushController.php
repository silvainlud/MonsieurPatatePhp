<?php

declare(strict_types=1);

namespace App\Http\Main\Controller;

use App\Domain\NotificationSubscriber\IUserPushSubscriberService;
use App\Domain\User\Entity\AbstractUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/push/')]
class PushController extends AbstractController
{
    public function __construct(
        private readonly IUserPushSubscriberService $userPushSubscriberService,
    ) {
    }

    #[Route('subscribe')]
    public function register(Request $request): Response
    {
        $content = json_decode($request->getContent(), true);
        $endpoint = $content['endpoint'];
        if (!$this->userPushSubscriberService->exist($endpoint)) {
            $user = $this->getUser();
            if (!$user instanceof AbstractUser) {
                throw $this->createNotFoundException();
            }

            $this->userPushSubscriberService->register(
                $user,
                $endpoint,
                $content['keys']['p256dh'],
                $content['keys']['auth']
            );
        }

        return $this->json([]);
    }

    #[Route('key')]
    public function key(): Response
    {
        return $this->json([
            'key' => $this->userPushSubscriberService->getPublicKey(),
        ]);
    }
}
