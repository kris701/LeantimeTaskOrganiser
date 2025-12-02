<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Domain\Setting\Services\Setting as SettingService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
use Leantime\Domain\Projects\Services\Projects as ProjectService;

use Leantime\Domain\Plugins\Services\Plugins as PluginsManager;

class SettingsController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.settings';

    private ProjectService $projectsService;
    private SettingService $settingsService;
    private PluginsManager $pluginsManager;

    public function init(
        ProjectService $projectsService,
        SettingService $settingsService,
        PluginsManager $pluginsManager,
    ) {
        $this->projectsService = $projectsService;
        $this->settingsService = $settingsService;
        $this->pluginsManager = $pluginsManager;

        session(['lastPage' => BASE_URL.'/dashboard/home']);
    }
	
	public function get(){
		if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests!');
        }

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        //$this->settingsService->saveSetting($sortingKey, (new SettingsIndex(""))->Serialize());
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);
        
        usort($settingsIndex->indexes, function($a, $b) { return $b->order - $a->order; });

		$this->tpl->assign('settings', $settingsIndex);
        $this->tpl->assign('exportData', null);
		$this->getEnabledPlugins();
	}

    public function add(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $name = $this->incomingRequest->get("name");
        $subtitle = $this->incomingRequest->get("subtitle");
        
        $maxtasks = $this->incomingRequest->get("maxtasks");
        $persistency = $this->incomingRequest->get("persistency");
        $shownbydefault = $this->incomingRequest->get("shownbydefault");
        $order = $this->incomingRequest->get("order");
        $hideifempty = $this->incomingRequest->get("hideifempty");

        $includetasks = $this->incomingRequest->get("includetasks");
        $includesubtasks = $this->incomingRequest->get("includesubtasks");
        $includebugs = $this->incomingRequest->get("includebugs");

        $modules = $this->incomingRequest->get("modules");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $id = max(array_map( function($v) { return $v->id; } ,$settingsIndex->indexes)) + 1;
        $settingsIndex->indexes[$id] = new SettingsModel(array());
        $settingsIndex->indexes[$id]->id = $id;
        $settingsIndex->indexes[$id]->name = $name;
        $settingsIndex->indexes[$id]->subtitle = $subtitle;

        $settingsIndex->indexes[$id]->maxtasks = $maxtasks;
        $settingsIndex->indexes[$id]->persistency = $persistency;
        $settingsIndex->indexes[$id]->shownbydefault = isset($shownbydefault);
        $settingsIndex->indexes[$id]->order = $order;
        $settingsIndex->indexes[$id]->hideifempty = isset($hideifempty);

        $settingsIndex->indexes[$id]->includetasks = isset($includetasks);
        $settingsIndex->indexes[$id]->includesubtasks = isset($includesubtasks);
        $settingsIndex->indexes[$id]->includebugs = isset($includebugs);

        $settingsIndex->indexes[$id]->modules = json_decode($modules);

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());
        
        $this->tpl->assign('settings', $settingsIndex);
        $this->tpl->assign('exportData', null);
        $this->getEnabledPlugins();

        $this->tpl->setNotification("Task list added!", 'success');
    }

    public function save(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $id = $this->incomingRequest->get("id");
        $name = $this->incomingRequest->get("name");
        $subtitle = $this->incomingRequest->get("subtitle");

        $maxtasks = $this->incomingRequest->get("maxtasks");
        $persistency = $this->incomingRequest->get("persistency");
        $shownbydefault = $this->incomingRequest->get("shownbydefault");
        $order = $this->incomingRequest->get("order");
        $hideifempty = $this->incomingRequest->get("hideifempty");

        $includetasks = $this->incomingRequest->get("includetasks");
        $includesubtasks = $this->incomingRequest->get("includesubtasks");
        $includebugs = $this->incomingRequest->get("includebugs");

        $modules = $this->incomingRequest->get("modules");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $settingsIndex->indexes[$id]->name = $name;
        $settingsIndex->indexes[$id]->subtitle = $subtitle;

        $settingsIndex->indexes[$id]->maxtasks = $maxtasks;
        $settingsIndex->indexes[$id]->persistency = $persistency;
        $settingsIndex->indexes[$id]->shownbydefault = isset($shownbydefault);
        $settingsIndex->indexes[$id]->order = $order;
        $settingsIndex->indexes[$id]->hideifempty = isset($hideifempty);

        $settingsIndex->indexes[$id]->includetasks = isset($includetasks);
        $settingsIndex->indexes[$id]->includesubtasks = isset($includesubtasks);
        $settingsIndex->indexes[$id]->includebugs = isset($includebugs);

        $settingsIndex->indexes[$id]->modules = json_decode($modules);

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());
        
        $this->tpl->assign('settings', $settingsIndex);
        $this->tpl->assign('exportData', null);
        $this->getEnabledPlugins();

        $this->tpl->setNotification("Task list saved!", 'success');
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
        $this->tpl->assign('exportData', null);
        $this->getEnabledPlugins();

        $this->tpl->setNotification("Task list deleted!", 'success');
    }

    private function getEnabledPlugins(){
        $allEnabledPlugins = array_column(array_filter($this->pluginsManager->getEnabledPlugins(), function($v) {
            return $v->enabled;
        }), 'name');
        $this->tpl->assign('availableplugins', array(
            "common" => true,
            "customfields" => in_array("Custom Fields", $allEnabledPlugins)
        ));
    }

    public function export(){
        if (! $this->incomingRequest->getMethod() == 'GET') {
            throw new Error('This endpoint only supports GET requests');
        }

        $id = $this->incomingRequest->get("id");

        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $this->get();
        $this->tpl->assign('exportData', json_encode($settingsIndex->indexes[$id]));
    }

    public function importFile(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $targetFile = $_FILES['file'];
        $text = file_get_contents($targetFile["tmp_name"]);
        $object = json_decode($text);
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $maxId = max(array_column($settingsIndex->indexes, 'id'));
        $object->id = $maxId + 1;

        $settingsIndex->indexes[$object->id] = $object;

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());

        $this->get();
    }

    public function import(){
        if (! $this->incomingRequest->getMethod() == 'POST') {
            throw new Error('This endpoint only supports POST requests');
        }

        $text = $this->incomingRequest->get("data");
        $object = json_decode($text);
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);

        $maxId = max(array_column($settingsIndex->indexes, 'id'));
        $object->id = $maxId + 1;

        $settingsIndex->indexes[$object->id] = $object;

        $this->settingsService->saveSetting($sortingKey, $settingsIndex->Serialize());

        $this->get();
    }
}
