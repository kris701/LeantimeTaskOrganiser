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
    }
	
	public function get(){
        $settingsIndex = $this->GetSettingsIndex();
        
        usort($settingsIndex->indexes, function($a, $b) { return $b->order - $a->order; });

		$this->tpl->assign('settings', $settingsIndex);
        $this->tpl->assign('exportData', null);
		$this->getEnabledPlugins();
	}

    public function add(){
        $settingsIndex = $this->GetSettingsIndex();

        $id = max(array_map( function($v) { return $v->id; } ,$settingsIndex->indexes)) + 1;
        $settingsIndex->indexes[$id] = new SettingsModel(array());
        $settingsIndex->indexes[$id]->id = $id;

        $this->SetSettingsDataByRequest($settingsIndex);
        $this->SaveSettingsIndex($settingsIndex);
        
        $this->tpl->setNotification("Task list added!", 'success');
        $this->get();
    }

    public function save(){
        $settingsIndex = $this->GetSettingsIndex();
        $this->SetSettingsDataByRequest($settingsIndex);
        $this->SaveSettingsIndex($settingsIndex);
        
        $this->tpl->setNotification("Task list saved!", 'success');
        $this->get();
    }

    public function delete(){
        $id = $this->incomingRequest->get("id");

        $settingsIndex = $this->GetSettingsIndex();
        unset($settingsIndex->indexes[$id]);
        $this->SaveSettingsIndex($settingsIndex);
        
        $this->tpl->setNotification("Task list deleted!", 'success');
        $this->get();
    }

    public function export(){
        $id = $this->incomingRequest->get("id");

        $settingsIndex = $this->GetSettingsIndex();

        $this->get();
        $this->tpl->assign('exportData', json_encode($settingsIndex->indexes[$id]));
    }

    public function importFile(){
        $targetFile = $_FILES['file'];
        $text = file_get_contents($targetFile["tmp_name"]);
        $object = json_decode($text);
        
        $settingsIndex = $this->GetSettingsIndex();

        $maxId = max(array_column($settingsIndex->indexes, 'id'));
        $object->id = $maxId + 1;

        $settingsIndex->indexes[$object->id] = $object;

        $this->SaveSettingsIndex($settingsIndex);

        $this->get();
    }

    public function import(){
        $text = $this->incomingRequest->get("data");
        $object = json_decode($text);
        
        $settingsIndex = $this->GetSettingsIndex();

        $maxId = max(array_column($settingsIndex->indexes, 'id'));
        $object->id = $maxId + 1;

        $settingsIndex->indexes[$object->id] = $object;

        $this->SaveSettingsIndex($settingsIndex);

        $this->get();
    }

    private function getEnabledPlugins(){
        $allEnabledPlugins = array_column(array_filter($this->pluginsManager->getEnabledPlugins(), function($v) {
            return $v->enabled;
        }), 'name');
        $this->tpl->assign('availableplugins', array(
            "common" => true,
            "customfields" => in_array("Custom Fields", $allEnabledPlugins),
            "strategies" => in_array("Leantime Strategies", $allEnabledPlugins),
            "plans" => in_array("Program Plans", $allEnabledPlugins)
        ));
    }

    private function GetSettingsIndex() : SettingsIndex{
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $settingDataStr = $this->settingsService->getSetting($sortingKey);
        $settingsIndex = new SettingsIndex($settingDataStr);
        return $settingsIndex;
    }

    private function SaveSettingsIndex(SettingsIndex $index){
        $userId = session('userdata.id');
        $sortingKey = "user.{$userId}.taskorganisersettings";
        $this->settingsService->saveSetting($sortingKey, $index->Serialize());
    }

    private function SetSettingsDataByRequest(SettingsIndex $index){
        $id = $this->incomingRequest->get("id");
        $index->indexes[$id]->name = $this->incomingRequest->get("name");
        $index->indexes[$id]->subtitle = $this->incomingRequest->get("subtitle");

        $index->indexes[$id]->maxtasks = $this->incomingRequest->get("maxtasks");
        $index->indexes[$id]->persistency = $this->incomingRequest->get("persistency");
        $index->indexes[$id]->shownbydefault = null !== ($this->incomingRequest->get("shownbydefault"));
        $index->indexes[$id]->order = $this->incomingRequest->get("order");
        $index->indexes[$id]->hideifempty = null !== ($this->incomingRequest->get("hideifempty"));
        $index->indexes[$id]->showweights = null !== ($this->incomingRequest->get("showweights"));

        $index->indexes[$id]->includetasks = null !== ($this->incomingRequest->get("includetasks"));
        $index->indexes[$id]->includesubtasks = null !== ($this->incomingRequest->get("includesubtasks"));
        $index->indexes[$id]->includebugs = null !== ($this->incomingRequest->get("includebugs"));

        $index->indexes[$id]->modules = json_decode($this->incomingRequest->get("modules"));
    }
}
