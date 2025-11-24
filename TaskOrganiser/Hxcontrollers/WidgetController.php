<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Plugins\TaskOrganiser\Services\SortingService;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;

class WidgetController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.widget';

    private ProjectService $projectsService;
    private TicketService $ticketsService;
    private SortingService $sortingService;
    private Setting $settingsService;

    public function init(
        ProjectService $projectsService,
        TicketService $ticketsService,
        SortingService $sortingService,
        Setting $settingsService,
    ) {
        $this->projectsService = $projectsService;
        $this->ticketsService = $ticketsService;
        $this->sortingService = $sortingService;
        $this->settingsService = $settingsService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests!');
        }

        $userId = session('userdata.id');
        $tasks = $this->sortingService->Calculate();

        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);        

        // Return needed items
		$this->tpl->assign('settings', $settingsIndex);
		$this->tpl->assign('tasks', $tasks);
		$this->tpl->assign('statusLabels', $this->ticketsService->getAllStatusLabelsByUserId($userId));
		$this->tpl->assign('effortLabels', $this->ticketsService->getEffortLabels());
		$this->tpl->assign('priorityLabels', $this->ticketsService->getPriorityLabels());
	}
}
