<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Plugins\TaskOrganiser\Services\SortingService;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
use Leantime\Plugins\TaskOrganiser\Repositories\CacheRepository;

class WidgetController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.widget';

    private TicketService $ticketsService;
    private SortingService $sortingService;
    private Setting $settingsService;
    private CacheRepository $cacheRepository;

    public function init(
        TicketService $ticketsService,
        SortingService $sortingService,
        Setting $settingsService,
        CacheRepository $cacheRepository,
    ) {
        $this->ticketsService = $ticketsService;
        $this->sortingService = $sortingService;
        $this->settingsService = $settingsService;
        $this->cacheRepository = $cacheRepository;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
        $this->GetData();
	}

    public function clearCache(){
        $id = $this->incomingRequest->get("id");

        $this->sortingService->clearCache($id);
        $this->GetData();
    }

    public function ignoreTask(){
        $taskId = $this->incomingRequest->get("taskId");
        $settingId = $this->incomingRequest->get("settingId");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);
        $setting = $settingsIndex->indexes[$settingId];

        if ($setting != null && $setting->allowignoring == true){
            $cacheKey = "user.{$userId}.{$setting->id}";
            $cache = $this->cacheRepository->getCache($cacheKey);
            if ($cache){
                $cacheTasks = json_decode($cache->tasklist);
                $newCacheTasks = [];
                foreach($cacheTasks as $id)
                    if ($id != $taskId)
                        array_push($newCacheTasks, $id);
                $cache->tasklist = json_encode($newCacheTasks);
                $this->cacheRepository->addCache($cache);
            }
        }

        $this->GetData();
    }

    private function GetData(){
        $userId = session('userdata.id');
        $tasks = $this->sortingService->Calculate();

        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $indexes = array_filter($settingsIndex->indexes, function($v) use($tasks){
            if (count($tasks[$v->id]) == 0 && $v->hideifempty == true)
                return false;
            return true;
        });

        usort($indexes, function($a, $b) { return $b->order - $a->order; });

        // Return needed items
		$this->tpl->assign('settings', $indexes);
		$this->tpl->assign('tasks', $tasks);
		$this->tpl->assign('statusLabels', $this->ticketsService->getAllStatusLabelsByUserId($userId));
		$this->tpl->assign('effortLabels', $this->ticketsService->getEffortLabels());
		$this->tpl->assign('priorityLabels', $this->ticketsService->getPriorityLabels());
		$this->tpl->assign('expirations', $this->sortingService->GetCacheExpirations());
    }
}
