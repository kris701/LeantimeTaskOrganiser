<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;

class TaskOrganiserWidget extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.taskOrganiserWidget';

    private TicketService $ticketsService;

    private Setting $settingsService;

    public function init(
        TicketService $ticketsService,
        Setting $settingsService,
    ) {
        $this->ticketsService = $ticketsService;
        $this->settingsService = $settingsService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests!');
        }

        $params = $this->incomingRequest->query->all();

        $userId = session('userdata.id');
        $searchCriteria = array(
            "status"=>"1,2,3,4",
            "type"=>"task"
        );
        $relevantTasks = $this->ticketsService->getAll($searchCriteria);
	
        // Do whatever with the tasks here
		
        // Return needed items
		$this->tpl->assign('allTickets', $relevantTasks);
		$this->tpl->assign('statusLabels', $this->ticketsService->getAllStatusLabelsByUserId($userId));
	}
}
