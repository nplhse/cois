<?php

namespace App\Controller\Settings;

use App\Entity\Setting;
use App\Form\SettingType;
use App\Repository\SettingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class SettingsController extends AbstractController
{
    #[Route('/settings/config', name: 'app_settings_config')]
    public function index(SettingRepository $settingRepository): Response
    {
        $settings = $settingRepository->findAllSettings();

        return $this->render('settings/system/index.html.twig', [
            'settings' => $settings,
        ]);
    }

    #[Route('/settings/config/{id}', name: 'app_settings_config_edit')]
    public function edit(Setting $setting, Request $request, SettingRepository $settingRepository): Response
    {
        $form = $this->createForm(SettingType::class, $setting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $settingRepository->add($setting);

            return $this->redirectToRoute('app_settings_config');
        }

        return $this->renderForm('settings/system/edit.html.twig', [
            'form' => $form,
            'setting' => $setting,
        ]);
    }
}
