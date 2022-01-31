<?php

namespace App\EventSubscriber;

use App\Domain\Event\User\UserRegistered;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;

class NotifyAdminSubscriber implements EventSubscriberInterface
{
    private UserRepositoryInterface $userRepository;

    private MailerInterface $mailer;

    private string $mailerSender;

    private string $mailerFrom;

    public function __construct(UserRepositoryInterface $userRepository, MailerInterface $mailer, string $mailerSender, string $mailerFrom)
    {
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->mailerSender = $mailerSender;
        $this->mailerFrom = $mailerFrom;
    }

    public function sendNewUserNotification(UserRegistered $event): void
    {
        $user = $event->getUser();

        foreach ($this->userRepository->getAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
                ->subject('A new User has been created')
                ->htmlTemplate('emails/notification/user_new.inky.twig')
                ->context(['user' => $user]);

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            UserRegistered::NAME => ['sendNewUserNotification', -10],
        ];
    }
}
