<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Repository\ImportRepository;
use Domain\Contracts\UserInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent('onboarding')]
class OnboardingComponent
{
    public UserInterface $user;

    public ?int $step = null;

    public function __construct(
        private ImportRepository $importRepository,
    ) {
    }

    public function mount(UserInterface $user): void
    {
        $this->user = $user;
        $this->step = $this->getStep();
    }

    private function getStep(): ?int
    {
        if (!$this->user->isParticipant()) {
            return 1;
        }

        if (0 === $this->user->getHospitals()->count()) {
            return 2;
        }

        if (0 === $this->importRepository->countImportsByUser($this->user)) {
            return 3;
        }

        return null;
    }
}
