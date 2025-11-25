<?php

namespace Leantime\Plugins\TaskOrganiser\Services;

use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
use Leantime\Plugins\TaskOrganiser\Models\CachedTaskList;
use Leantime\Plugins\TaskOrganiser\Repositories\CacheRepository;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\StatusSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\PrioritySortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\ClientSortModule;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Domain\Setting\Services\Setting as SettingService;

class SortingService
{
    private TicketService $ticketsService;
    private SettingService $settingsService;
    private ProjectService $projectsService;
    
    private CacheRepository $cacheRepository;

    public function __construct(
        TicketService $ticketsService,
        SettingService $settingsService,
        ProjectService $projectsService,
        CacheRepository $cacheRepository,
    ) {
        $this->ticketsService = $ticketsService;
        $this->settingsService = $settingsService;
        $this->projectsService = $projectsService;
        $this->cacheRepository = $cacheRepository;
    }

    public function ClearCache(string $id) {
        $userId = session('userdata.id');
        $cacheKey = "user.{$userId}.{$id}";
        $this->cacheRepository->deleteCache($cacheKey);
    }

    public function Calculate() : array {
        $userId = session('userdata.id');
        $searchCriteria = array(
            "type"=>"task"
        );
        $tasks = $this->ticketsService->getAll($searchCriteria);
        $settings = $this->GetSettings();

        $tickets = array();

        $date_utc = new \DateTime('now', new \DateTimeZone('UTC'));

        foreach($settings->indexes as $setting){
            $targetTasks = $tasks;

            // Check cache for existing
            $cacheKey = "user.{$userId}.{$setting->id}";
            if ($setting->persistency > 0){
                $cache = $this->cacheRepository->getCache($cacheKey);
                if ($cache){
                    $expires = new \DateTime($cache->expires, new \DateTimeZone('UTC'));
                    if ($expires > $date_utc)
                    {
                        $cacheTasks = json_decode($cache->tasklist);
                        $targetTasks = array_filter(
                            $targetTasks, 
                            function($value) use ($cacheTasks) { 
                                return in_array((string)$value['id'], $cacheTasks); 
                            }
                        );
                    }
                }
            }
            
            $newList = $this->CalculateTaskList($targetTasks, $setting);
            $tickets[$setting->id] = $newList;

            // Save cache
            if ($setting->persistency > 0) {
                $date_utc_modified = (clone $date_utc)->add(new \DateInterval("PT{$setting->persistency}H"));
                $newCache = new CachedTaskList();
                $newCache->id = $cacheKey;
                $newCache->expires = $date_utc_modified->format('Y-m-d H:i:s');
                $newCache->tasklist = json_encode(array_map(function ($v) { return $v->id; }, $newList));
                $this->cacheRepository->addCache($newCache);
            }
        }

        return $tickets;
    }

    function CalculateTaskList(array $tasks, object $setting){
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
        return array_slice($settingResult, 0, $setting->maxtasks);
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
                        case 'priority':
                            array_push($setting->modules, new PrioritySortModule($moduleSetting));
                            break;
                        case 'client':
                            array_push($setting->modules, new ClientSortModule($moduleSetting));
                            break;
                    }
                }
            }
        }

        return $settings;
    }
}
