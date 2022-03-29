<?php

namespace App\Command;

use App\Entity\Setting;
use App\Repository\SettingRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:settings:load',
    description: 'Load all the default settings',
)]
class LoadSettingsCommand extends Command
{
    private SettingRepository $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        parent::__construct();

        $this->settingRepository = $settingRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->settingRepository->clearAllSettings();

            $this->addTitle();
            $this->addRegistration();
            $this->addCookieConsent();
            $this->addTerms();
        } catch (\Exception $e) {
            $io->warning(sprintf('Something went wrong! Failed to load Settings: %s.', $e->getMessage()));

            return Command::FAILURE;
        }

        $io->success('Settings have been loaded.');

        return Command::SUCCESS;
    }

    private function addTitle(): void
    {
        $setting = new Setting();
        $setting->setName('title');
        $setting->setCategory('general');
        $setting->setType('string');
        $setting->setValue('Collaborative IVENA statistics');

        $this->settingRepository->add($setting, false);
    }

    private function addRegistration(): void
    {
        $setting = new Setting();
        $setting->setName('enable_registration');
        $setting->setCategory('user');
        $setting->setType('boolean');
        $setting->setValue('true');

        $this->settingRepository->add($setting, false);
    }

    private function addCookieConsent(): void
    {
        $setting = new Setting();
        $setting->setName('enable_cookie_consent');
        $setting->setCategory('user');
        $setting->setType('boolean');
        $setting->setValue('true');

        $this->settingRepository->add($setting, false);
    }

    private function addTerms(): void
    {
        $setting = new Setting();
        $setting->setName('enable_terms');
        $setting->setCategory('user');
        $setting->setType('boolean');
        $setting->setValue('true');

        $this->settingRepository->add($setting, true);
    }
}
