<?php

namespace Leantime\Plugins\TaskOrganiser\Services;

use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\StatusSortModule;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Domain\Setting\Services\Setting as SettingService;

class SortingService
{
    private TicketService $ticketsService;
    private SettingService $settingsService;
    private ProjectService $projectsService;

    public function __construct(
        TicketService $ticketsService,
        SettingService $settingsService,
        ProjectService $projectsService,
    ) {
        $this->ticketsService = $ticketsService;
        $this->settingsService = $settingsService;
        $this->projectsService = $projectsService;
    }

    public function Calculate() : array {
        $userId = session('userdata.id');
        $searchCriteria = array(
            "type"=>"task"
        );
        $tasks = $this->ticketsService->getAll($searchCriteria);
        $settings = $this->GetSettings();

        $tickets = array();

        foreach($settings->indexes as $setting){
            $settingResult = [];
            foreach($tasks as $task){
                $newTask = new TicketModel($task);
                $weight = 0;
                foreach($setting->modules as $module)
                    $weight += $module->Calculate($newTask);
                $newTask->weight = $weight;

                array_push($settingResult, $newTask);
            }
            usort($settingResult, function ($a, $b) {
                return $b->weight - $a->weight;
            });
            $tickets[$setting->id] = $settingResult;
        }

        return $tickets;
    }

    function GetSettings() : SettingsIndex {
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settings = new SettingsIndex($settingDataStr);

        foreach($settings->indexes as $setting){
            $moduleSettings = $setting->modules;
            $setting->modules = [];
            if ($moduleSettings != null){
                foreach($moduleSettings as $moduleSetting){
                    switch($moduleSetting->type){
                        case 'status':
                            array_push($setting->modules, new StatusSortModule($moduleSetting));
                            break;
                    }
                }
            }
        }

        return $settings;
    }
}
