<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Repositories\Setting as SettingRepository;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;

class TaskOrganiserWidget extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.taskOrganiserWidget';

    private TicketService $ticketsService;

    private SettingRepository $settingRepo;

    public function init(
        TicketService $ticketsService,
        SettingRepository $settingRepo,
    ) {
        $this->ticketsService = $ticketsService;
        $this->settingRepo = $settingRepo;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests');
        }
		
		$params = $this->incomingRequest->query->all();
		
		$this->tpl->assign('allTickets', $this->ticketsService->getAll());
	}
}
