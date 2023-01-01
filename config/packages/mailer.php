<?php

declare(strict_types=1);

use Symfony\Config\Framework\MailerConfig;

return static function (MailerConfig $mailerConfig): void {
    $mailerConfig->dsn('%env(MAILER_DSN)%');
};
