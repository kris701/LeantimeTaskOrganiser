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
use Leantime\Plugins\TaskOrganiser\Services\SortModules\DueDateSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\EffortSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\TopNEffortSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\ProjectSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\MilestoneSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\CreatedWithinSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\CustomFieldsRadioSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\CustomFieldsBoolSortModule;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\CustomFieldsCheckboxSortModule;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Domain\Setting\Services\Setting as SettingService;
use Leantime\Domain\Plugins\Services\Plugins as PluginsManager;
use Leantime\Core\Configuration\Environment;
use Leantime\Core\Db\Db;

class SortingService
{
    private TicketService $ticketsService;
    private SettingService $settingsService;
    private ProjectService $projectsService;
    private PluginsManager $pluginsManager;
    
    private CacheRepository $cacheRepository;

    private Db $db;
    private Environment $config;

    public function __construct(
        TicketService $ticketsService,
        SettingService $settingsService,
        ProjectService $projectsService,
        CacheRepository $cacheRepository,
        Db $db,
        Environment $config,
        PluginsManager $pluginsManager,
    ) {
        $this->ticketsService = $ticketsService;
        $this->settingsService = $settingsService;
        $this->projectsService = $projectsService;
        $this->cacheRepository = $cacheRepository;
        $this->pluginsManager = $pluginsManager;
        $this->db = $db;
        $this->config = $config;
    }

    public function ClearCache(string $id) {
        $userId = session('userdata.id');
        $cacheKey = "user.{$userId}.{$id}";
        $this->cacheRepository->deleteCache($cacheKey);
    }

    public function Calculate() : array {
        $userId = session('userdata.id');
        $searchCriteria = array(
            "type"=>"task,subtask",
            "users"=>$userId
        );
        $tasks = $this->ticketsService->getAll($searchCriteria);
        $settings = $this->GetSettings();

        $tickets = array();

        $date_utc = new \DateTime('now', new \DateTimeZone('UTC'));

        foreach($settings->indexes as $setting){
            $targetTasks = array_filter(
                $tasks, 
                function($v) use ($setting){
                    if ($setting->includetasks && $v['type'] == "task")
                        return true;
                    if ($setting->includesubtasks && $v['type'] == "subtask")
                        return true;
                    if ($setting->includebugs && $v['type'] == "bug")
                        return true;
                    return false;
            });

            // Check cache for existing
            $hasExpired = true;
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
                        $hasExpired = false;
                    }
                }
            }
            
            $newList = $this->CalculateTaskList($targetTasks, $setting);
            $tickets[$setting->id] = array_filter($newList, function($v){ return $v->weight >= 0; });

            // Save cache
            if ($setting->persistency > 0) {
                $newCache = new CachedTaskList();
                $newCache->id = $cacheKey;
                if($hasExpired){
                    $date_utc_modified = (clone $date_utc)->add(new \DateInterval("PT{$setting->persistency}H"));
                    $newCache->expires = $date_utc_modified->format('c');
                }
                else {
                    $cache = $this->cacheRepository->getCache($cacheKey);
                    $newCache->expires = $cache->expires;
                }
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
        $enabledPlugins = $this->getEnabledPlugins();

        foreach($settings->indexes as $setting){
            $moduleSettings = $setting->modules;
            $setting->modules = [];
            if ($moduleSettings != null){
                foreach($moduleSettings as $moduleSetting){
                    switch($moduleSetting->type){
                        // Common
                        case 'status':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new StatusSortModule($moduleSetting, $this->projectsService, $this->ticketsService));
                            break;
                        case 'priority':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new PrioritySortModule($moduleSetting));
                            break;
                        case 'client':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new ClientSortModule($moduleSetting));
                            break;
                        case 'project':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new ProjectSortModule($moduleSetting, $this->projectsService));
                            break;
                        case 'duedate':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new DueDateSortModule($moduleSetting));
                            break;
                        case 'effort':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new EffortSortModule($moduleSetting));
                            break;
                        case 'milestone':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new MilestoneSortModule($moduleSetting, $this->ticketsService, $this->projectsService));
                            break;
                        case 'topneffort':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new TopNEffortSortModule($moduleSetting));
                            break;
                        case 'createdwithin':
                            if ($this->isPluginEnalbed($enabledPlugins, "common"))
                                array_push($setting->modules, new CreatedWithinSortModule($moduleSetting));
                            break;

                        // Custom fields
                        case 'customfields_radio':
                            if ($this->isPluginEnalbed($enabledPlugins, "customfields"))
                                array_push($setting->modules, new CustomFieldsRadioSortModule($this->db, $this->config, $moduleSetting));
                            break;
                        case 'customfields_bool':
                            if ($this->isPluginEnalbed($enabledPlugins, "customfields"))
                                array_push($setting->modules, new CustomFieldsBoolSortModule($this->db, $this->config, $moduleSetting));
                            break;
                        case 'customfields_checkbox':
                            if ($this->isPluginEnalbed($enabledPlugins, "customfields"))
                                array_push($setting->modules, new CustomFieldsCheckboxSortModule($this->db, $this->config, $moduleSetting));
                            break;
                    }
                }
            }
        }

        return $settings;
    }

    function GetCacheExpirations() : array {
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settings = new SettingsIndex($settingDataStr);

        $expirations = [];

        foreach($settings->indexes as $setting){
            $cacheKey = "user.{$userId}.{$setting->id}";
            if ($setting->persistency > 0){
                $cache = $this->cacheRepository->getCache($cacheKey);
                if ($cache){
                    $expirations[$setting->id] = $cache->expires;
                }
                else
                    $expirations[$setting->id] = "Always";
            }
            else
                $expirations[$setting->id] = "Always";
        }

        return $expirations;
    }

    private function getEnabledPlugins() : array{
        $allEnabledPlugins = array_column(array_filter($this->pluginsManager->getEnabledPlugins(), function($v) {
            return $v->enabled;
        }), 'name');
        $availableplugins = array(
            "common" => true,
            "customfields" => in_array("Custom Fields", $allEnabledPlugins)
        );
        return $availableplugins;
    }

    private function isPluginEnalbed(array $availableplugins, string $key) : bool{
        return array_key_exists($key, $availableplugins) && $availableplugins[$key];
    }
}
