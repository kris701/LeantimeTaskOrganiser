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

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.mytaskorganisersorting";
        $sorting = $this->settingsService->getSetting($sortingKey);
		
		$params = $this->incomingRequest->query->all();
		
		$this->tpl->assign('allTickets', $this->ticketsService->getAll());
	}

    public function saveSorting(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests!');
        }

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.mytaskorganisersorting";

        $this->settingsService->saveSetting($sortingKey, json_encode($taskList));
    }
}
