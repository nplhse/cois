<?php

namespace App\Twig;

use App\Service\CookieConsentService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CookieConsentExtension extends AbstractExtension
{
    private CookieConsentService $consentService;

    public function __construct(CookieConsentService $consentService)
    {
        $this->consentService = $consentService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'cookie_consent_render',
                [$this->consentService, 'render'],
                ['is_safe' => ['html']]
            ),
            new TwigFunction(
                'has_cookie_consent',
                [$this->consentService, 'hasCookieConsent'],
            ),
        ];
    }
}
