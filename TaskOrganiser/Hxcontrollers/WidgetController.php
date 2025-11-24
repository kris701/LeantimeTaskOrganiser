<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Plugins\TaskOrganiser\Services\SortingService;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;

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
        $globalTasks = $this->sortingService->CalculateGlobal();
        $todaysTasks = $this->sortingService->CalculateToday($globalTasks);
		
        // Return needed items
		$this->tpl->assign('globalTasks', $globalTasks);
		$this->tpl->assign('todaysTasks', $todaysTasks);
		$this->tpl->assign('statusLabels', $this->ticketsService->getAllStatusLabelsByUserId($userId));
		$this->tpl->assign('effortLabels', $this->ticketsService->getEffortLabels());
		$this->tpl->assign('priorityLabels', $this->ticketsService->getPriorityLabels());
	}
}
