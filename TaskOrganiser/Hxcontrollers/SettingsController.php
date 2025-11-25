<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting as SettingService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
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
        $sortingKey = "user.{$userId}.taskorganisersettings";
        //$this->settingsService->saveSetting($sortingKey, (new SettingsIndex(""))->Serialize());
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);
        
		$this->tpl->assign('settings', $settingsIndex);
	}

    public function add(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $name = $this->incomingRequest->get("name");
        $subtitle = $this->incomingRequest->get("subtitle");
        $modules = $this->incomingRequest->get("modules");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $id = max(array_map( function($v) { return $v->id; } ,$settingsIndex->indexes)) + 1;
        $settingsIndex->indexes[$id] = new SettingsModel();
        $settingsIndex->indexes[$id]->id = $id;
        $settingsIndex->indexes[$id]->name = $name;
        $settingsIndex->indexes[$id]->subtitle = $subtitle;
        $settingsIndex->indexes[$id]->modules = json_decode($modules);

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());
        
        $this->tpl->assign('settings', $settingsIndex);
    }

    public function save(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $id = $this->incomingRequest->get("id");
        $name = $this->incomingRequest->get("name");
        $subtitle = $this->incomingRequest->get("subtitle");
        $modules = $this->incomingRequest->get("modules");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $settingsIndex->indexes[$id]->name = $name;
        $settingsIndex->indexes[$id]->subtitle = $subtitle;
        $settingsIndex->indexes[$id]->modules = json_decode($modules);

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());
        
        $this->tpl->assign('settings', $settingsIndex);
    }

    public function delete(){
        if (! $this->incomingRequest->getMethod() == 'DELETE') {
            throw new Error('This endpoint only supports DELETE requests');
        }

        $id = $this->incomingRequest->get("id");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        unset($settingsIndex->indexes[$id]);

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());
        
        $this->tpl->assign('settings', $settingsIndex);
    }
}
