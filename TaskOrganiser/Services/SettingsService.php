<?php

namespace Leantime\Plugins\TaskOrganiser\Services;

use Leantime\Core\Db\Db as DbCore;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;

class SettingsService
{
    private Setting $settings;

    public function __construct(
        protected DbCore $db,
        Setting $settings,
    ) {
        $this->db = $db;
        $this->settings = $settings;
    }

    public function GetSettingsIndex(string $userId) : SettingsIndex{
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settings->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);
        return $settingsIndex;
    }

    public function SetSettingsIndex(SettingsIndex $index, string $userId){
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $this->settings->saveSetting($sortingKey, $index->Serialize());
    }
}
