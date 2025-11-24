<?php

namespace Leantime\Plugins\TaskOrganiser\Services;

use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SortModules\BaseSortModule;
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

    public function CalculateGlobal() : array {
        $userId = session('userdata.id');
        $searchCriteria = array(
            "type"=>"task"
        );
        $relevantTasks = $this->ticketsService->getAll($searchCriteria);
        $settings = $this->GetAllSettings();

        $tickets = array();

        foreach($relevantTasks as $task){
            $relevantSettings = $settings[$task['projectId']];

            if ($relevantSettings != null){
                $weight = 0;
                foreach($relevantSettings->modules as $module)
                    $weight += $module.Calculate($task);
                $task['weight'] = $weight + $relevantSettings->globalWeight;
            }
            else
                $task['weight'] = -1;

            array_push($tickets, $task);
        }
        $this->Sort($tickets);

        return $tickets;
    }

    function GetAllSettings() : array {
        $settings = array();

        $userId = session('userdata.id');
        $projects = $this->projectsService->getProjectsAssignedToUser($userId);
        foreach($projects as $project) {
            $projectId = $project['id'];
            $sortingKey = "user.{$userId}.taskorganisersettings.{$projectId}";
            $settingDataStr = $this->settingsService->getSetting($sortingKey);
            $thisProjectSettings = new SettingsModel($settingDataStr);
            $settings[$project['id']] = $thisProjectSettings;
        }

        return $settings;
    }

    public function CalculateToday(array $tickets) : array {
        $settings = $this->GetAllSettings();

        $todaysTickets = array();

        foreach($tickets as $task){
            $newTicket = $task;
            $relevantSettings = $settings[$newTicket['projectId']];

            if ($relevantSettings != null){
                $weight = 0;
                foreach($relevantSettings->modules as $module)
                    $weight += $module.Calculate($newTicket);
                $newTicket['weight'] = $weight + $relevantSettings->todaysWeight;
            }
            else
                $newTicket['weight'] = -1;

            array_push($tickets, $newTicket);
        }
        $this->Sort($todaysTickets);

        return $todaysTickets;
    }

    public function Sort(array $tickets){
        usort($tickets, function ($a, $b) {
            return $a['weight'] - $b['weight'];
        });
    }
}
