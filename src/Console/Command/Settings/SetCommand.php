<?php

namespace App\Console\Command\Settings;

use App\Console\Command\CommandAbstract;
use App\Entity;
use Symfony\Component\Console\Style\SymfonyStyle;

class SetCommand extends CommandAbstract
{
    public function __invoke(
        SymfonyStyle $io,
        Entity\Repository\SettingsTableRepository $settingsTableRepo,
        string $settingKey,
        string $settingValue
    ): int {
        $io->title('AzuraCast Settings');

        if (strtolower($settingValue) === 'null') {
            $settingsTableRepo->writeSettings([$settingKey => null]);

            $io->success(sprintf('Setting "%s" removed.', $settingKey));
            return 0;
        }

        if (0 === strpos($settingValue, '{')) {
            $settingValue = json_decode($settingValue, true, 512, JSON_THROW_ON_ERROR);
        }

        $settingsTableRepo->writeSettings([$settingKey => $settingValue]);

        $io->success(sprintf('Setting "%s" updated.', $settingKey));

        return 0;
    }
}
