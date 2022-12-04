<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\CookieConsent;
use App\Form\CookieConsentType;
use App\Repository\CookieConsentRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class CookieConsentService
{
    public const COOKIE_CONSENT = 'COOKIE_CONSENT';

    public function __construct(
        private CookieConsentRepository $repository,
        private FormFactoryInterface $formFactory,
        private Environment $twig,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function render(): string
    {
        $form = $this->createForm();

        return $this->twig->render('elements/cookie_consent.html.twig', [
            'consentForm' => $form->createView(),
        ]);
    }

    public function hasCookieConsent(Request $request): bool
    {
        if (!$request->cookies->has(self::COOKIE_CONSENT)) {
            return false;
        }

        $lookupKey = urldecode($request->cookies->get(self::COOKIE_CONSENT));
        $cookie = $this->repository->findOneBy(['lookupKey' => $lookupKey]);

        return null !== $cookie;
    }

    public function createForm(): \Symfony\Component\Form\FormInterface
    {
        return $this->formFactory->create(CookieConsentType::class, null, [
            'action' => $this->urlGenerator->generate('app_cookie_consent'),
        ]);
    }

    public function handleFormSubmit(array $formData, Request $request, Response $response): void
    {
        $lookupKey = bin2hex(random_bytes(16));

        if (in_array('necessary', $formData['categories'], true)) {
            $this->saveCookie(self::COOKIE_CONSENT, $lookupKey, $response);
            $this->saveConsent($lookupKey, $formData['categories'], $this->anonymizeIp($request->getClientIp()));
        }
    }

    public function saveCookie(string $name, string $value, Response $response): void
    {
        $expirationDate = new \DateTime();
        $expirationDate->add(new \DateInterval('P1Y'));

        $response->headers->setCookie(
            Cookie::create($name, $value)
                ->withExpires($expirationDate)
        );
    }

    public function saveConsent(string $lookupKey, array $categories, string $ipAddress): void
    {
        $consent = new CookieConsent();
        $consent->setLookupKey($lookupKey);
        $consent->setCategories($categories);
        $consent->setIpAddress($ipAddress);
        $consent->setCreatedAt(new \DateTime('NOW'));

        $this->repository->add($consent);
    }

    protected function anonymizeIp(?string $ip): string
    {
        if (null === $ip) {
            return 'Unknown';
        }

        $lastDot = strrpos($ip, '.') + 1;

        return substr($ip, 0, $lastDot).str_repeat('x', strlen($ip) - $lastDot);
    }
}
