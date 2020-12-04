<?php

namespace App\Form;

use App\Config;
use App\Entity;
use App\Environment;
use Doctrine\ORM\EntityManagerInterface;

class BrandingSettingsForm extends AbstractSettingsForm
{
    public function __construct(
        EntityManagerInterface $em,
        Entity\Repository\SettingsTableRepository $settingsRepo,
        Environment $environment,
        Config $config
    ) {
        $formConfig = $config->get('forms/branding', [
            'settings' => $environment,
        ]);

        parent::__construct(
            $em,
            $settingsRepo,
            $environment,
            $formConfig
        );
    }
}
