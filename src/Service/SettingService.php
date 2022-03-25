<?php

namespace App\Service;

use App\Entity\Setting;
use App\Repository\SettingRepository;

class SettingService
{
    private SettingRepository $settingRepository;

    private array $settings = [];

    private array $categories = [];

    public function __construct(SettingRepository $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    public function loadSettings(): void
    {
        $settings = $this->settingRepository->findAll();

        foreach ($settings as $setting) {
            $this->settings[$setting->getName()] = $setting;
            $this->categories[$setting->getCategory()][] = $setting->getName();
        }
    }

    public function getByName(string $name): ?Setting
    {
        if (empty($this->settings)) {
            $this->loadSettings();
        }

        if (array_key_exists($name, $this->settings)) {
            return $this->settings[$name];
        }

        return null;
    }

    public function getValueByName(string $name): bool|string|int|null
    {
        if (empty($this->settings)) {
            $this->loadSettings();
        }

        if (array_key_exists($name, $this->settings)) {
            return match ($this->settings[$name]->getType()) {
                'boolean' => filter_var($this->settings[$name]->getValue(), FILTER_VALIDATE_BOOLEAN),
                'integer' => filter_var($this->settings[$name]->getValue(), FILTER_VALIDATE_INT),
                'string' => $this->settings[$name]->getValue(),
                default => null,
            };
        }

        return null;
    }

    public function getByCategory(string $category): array
    {
        if (empty($this->settings)) {
            $this->loadSettings();
        }

        $settings = [];

        if (array_key_exists($category, $this->categories)) {
            foreach ($this->categories[$category] as $setting) {
                $settings[$setting] = $this->settings[$setting];
            }
        }

        return $settings;
    }
}
