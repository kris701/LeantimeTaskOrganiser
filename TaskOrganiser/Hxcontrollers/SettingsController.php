<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting as SettingService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;

use Leantime\Domain\Projects\Services\Projects as ProjectService;

class SettingsController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.settings';

    private ProjectService $projectsService;

    private SettingService $settingsService;

    public function init(
        ProjectService $projectsService,
        SettingService $settingsService,
    ) {
        $this->projectsService = $projectsService;
        $this->settingsService = $settingsService;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests!');
        }

        $params = $this->incomingRequest->query->all();

        $userId = session('userdata.id');
        $projects = $this->projectsService->getProjectsAssignedToUser($userId);
        $settings = array();

        foreach($projects as $project) {
            $projectId = $project['id'];
            $sortingKey = "user.{$userId}.taskorganisersettings.{$projectId}";
            $settingDataStr = $this->settingsService->getSetting($sortingKey);
            $thisProjectSettings = json_decode($settingDataStr);
            if ($settingDataStr == ''){
                $thisProjectSettings = new SettingsModel;
                $this->settingsService->saveSetting($sortingKey, json_encode($thisProjectSettings));
            }
            $settings[$project['id']] = $thisProjectSettings;
        }
        
        $this->tpl->assign('projects', $projects);
		$this->tpl->assign('settings', $settings);
	}

    public function save(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $project = $this->incomingRequest->get('project');
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings.{$project}";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $thisProjectSettings = json_decode($settingDataStr);

        $thisProjectSettings->globalWeight = intval($this->incomingRequest->get('globalWeight'));

        $this->settingsService->saveSetting($sortingKey, json_encode($thisProjectSettings));
    }
}
