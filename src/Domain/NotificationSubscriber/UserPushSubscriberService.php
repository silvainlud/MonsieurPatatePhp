<?php

declare(strict_types=1);

namespace App\Domain\NotificationSubscriber;

use App\Domain\NotificationSubscriber\Entity\UserPushSubscriber;
use App\Domain\NotificationSubscriber\Repository\UserPushSubscriberRepository;
use App\Domain\User\Entity\AbstractUser;
use Doctrine\ORM\EntityManagerInterface;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class UserPushSubscriberService implements IUserPushSubscriberService
{
    public function __construct(
        private readonly EntityManagerInterface       $em,
        private readonly UserPushSubscriberRepository $repository,
        private readonly string                       $pushPublicKey,
        private readonly WebPush                      $webPush,
    )
    {
    }

    public function register(AbstractUser $user, string $endpoint, string $p256dh, string $authKey): UserPushSubscriber
    {
        $userSubscribe = (new UserPushSubscriber($endpoint))
            ->setUser($user)
            ->setKeyP256dh($p256dh)
            ->setKeyAuth($authKey);

        $this->em->persist($userSubscribe);
        $this->em->flush();

        return $userSubscribe;
    }

    public function exist(string $endpoint): bool
    {
        return $this->repository->exist($endpoint);
    }

    public function send(UserPushSubscriber $subscriber, string $title, ?string $msg): void
    {
        $this->_addWebPUsh($subscriber, $title, $msg);

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($this->webPush->flush() as $ignore) {
        }
    }

    public function sendAll(string $title, ?string $msg): void
    {
        $subscribers = $this->em->getRepository(UserPushSubscriber::class)->findAll();

        /** @var UserPushSubscriber $subscriber */
        foreach ($subscribers as $subscriber) {
            $this->_addWebPUsh($subscriber, $title, $msg);
        }

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($this->webPush->flush() as $ignore) {
        }
    }

    public function getPublicKey(): string
    {
        return $this->pushPublicKey;
    }

    private function _addWebPUsh(UserPushSubscriber $subscriber, string $title, ?string $msg): void
    {
        $payload = json_encode([
            'message' => $msg ?? '',
            'title' => $title,
        ]);
        if ($payload === false) {
            throw new \InvalidArgumentException();
        }
        $this->webPush->queueNotification(
            Subscription::create([
                'endpoint' => $subscriber->getEndpoint(),
                'publicKey' => $subscriber->getKeyP256dh(),
                'authToken' => $subscriber->getKeyAuth(),
            ]),
            $payload
        );
    }

    /** {@inheritDoc} */
    public function getRegisteredUsers(): array
    {
        return $this->repository->getRegisteredUsers();
    }

    public function sendToUser(AbstractUser $user, string $title, ?string $msg): void
    {
        $subscribers = $this->repository->findBy(["user" => $user]);

        foreach ($subscribers as $subscriber) {
            $this->_addWebPUsh($subscriber, $title, $msg);
        }

        /** @noinspection PhpStatementHasEmptyBodyInspection */
        foreach ($this->webPush->flush() as $ignore) {
        }
    }
}
