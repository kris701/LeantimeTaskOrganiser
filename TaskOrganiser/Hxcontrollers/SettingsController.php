<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;

class SettingsController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.settings';

    private Setting $settingsService;

    public function init(
        Setting $settingsService,
    ) {
        $this->settingsService = $settingsService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests!');
        }

        $params = $this->incomingRequest->query->all();

        $userId = session('userdata.id');
        

		
        // Return needed items
		
	}
}
