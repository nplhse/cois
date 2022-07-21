<?php

namespace App\EventSubscriber;

use App\Domain\Event\Hospital\HospitalCreatedEvent;
use App\Domain\Event\Import\ImportFailedEvent;
use App\Domain\Event\Import\ImportSkippedRowEvent;
use App\Domain\Event\User\UserRegisteredEvent;
use App\Domain\Repository\UserRepositoryInterface;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class NotifyAdminSubscriber implements EventSubscriberInterface
{
    private UserRepositoryInterface $userRepository;

    private TranslatorInterface $translator;

    private UrlGeneratorInterface $router;

    private MailerInterface $mailer;

    private string $mailerSender;

    private string $mailerFrom;

    public function __construct(UserRepositoryInterface $userRepository, TranslatorInterface $translator, UrlGeneratorInterface $router, MailerInterface $mailer, string $appMailerSender, string $appMailerFrom)
    {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->router = $router;
        $this->mailer = $mailer;
        $this->mailerSender = $appMailerSender;
        $this->mailerFrom = $appMailerFrom;
    }

    public function sendHospitalCreatedNotification(HospitalCreatedEvent $event): void
    {
        $hospital = $event->getHospital();

        foreach ($this->userRepository->findAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
                ->subject('A new Hospital has been created')
                ->htmlTemplate('emails/notification/hospital_new.inky.twig')
                ->context(['hospital' => $hospital]);

            $this->mailer->send($email);
        }
    }

    public function sendImportFailedNotification(ImportFailedEvent $event): void
    {
        $import = $event->getImport();
        $exception = $event->getException();

        foreach ($this->userRepository->findAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_HIGH)
                ->subject('A new Import has failed')
                ->htmlTemplate('emails/notification/import_failed.inky.twig')
                ->context([
                    'import' => $import,
                    'exception' => $exception,
                ]);

            $this->mailer->send($email);
        }
    }

    public function sendImportSkippedRowNotification(ImportSkippedRowEvent $event): void
    {
        $import = $event->getImport();
        $exception = $event->getException();

        foreach ($this->userRepository->findAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_LOW)
                ->subject('Skipped row in Import')
                ->htmlTemplate('emails/notification/import_skipped.inky.twig')
                ->context([
                    'import' => $import,
                    'exception' => $exception,
                ]);

            $this->mailer->send($email);
        }
    }

    public function sendNewUserNotification(UserRegisteredEvent $event): void
    {
        $user = $event->getUser();

        foreach ($this->userRepository->findAdmins() as $admin) {
            $email = (new NotificationEmail())
                ->from(new Address($this->mailerSender, $this->mailerFrom))
                ->to(new Address($admin->getEmail()))
                ->importance(NotificationEmail::IMPORTANCE_MEDIUM)
                ->subject($this->translator->trans('notification.user.new.title', [], 'emails'))
                ->htmlTemplate('emails/notification/user_new.twig')
                ->action($this->translator->trans('notification.user.new.btn.review', [], 'emails'), $this->router->generate('app_settings_user_show', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL))
                ->context(
                    [
                        'username' => $user->getUsername(),
                        'userEmail' => $user->getEmail(),
                    ]
                );

            $this->mailer->send($email);
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            HospitalCreatedEvent::NAME => ['sendHospitalCreatedNotification', -10],
            ImportFailedEvent::NAME => ['sendImportFailedNotification', -10],
            ImportSkippedRowEvent::NAME => ['sendImportSkippedRowNotification', 0],
            UserRegisteredEvent::NAME => ['sendNewUserNotification', -10],
        ];
    }
}
