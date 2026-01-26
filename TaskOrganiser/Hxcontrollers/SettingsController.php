<?php

namespace Leantime\Plugins\TaskOrganiser\Hxcontrollers;

use Leantime\Core\Controller\HtmxController;
use Leantime\Plugins\TaskOrganiser\Services\SettingsService;
use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;
use Leantime\Plugins\TaskOrganiser\Models\SettingsIndex;
use Leantime\Plugins\TaskOrganiser\Repositories\CacheRepository;
use Leantime\Domain\Plugins\Services\Plugins as PluginsManager;

class SettingsController extends HtmxController
{
    protected static string $view = 'taskorganiser::partials.settings';

    private SettingsService $settingsService;
    private PluginsManager $pluginsManager;
    private CacheRepository $cacheRepository;

    public function init(
        SettingsService $settingsService,
        PluginsManager $pluginsManager,
        CacheRepository $cacheRepository,
    ) {
        $this->settingsService = $settingsService;
        $this->pluginsManager = $pluginsManager;
        $this->cacheRepository = $cacheRepository;
    }
	
	public function get(){
        $settingsIndex = $this->settingsService->GetSettingsIndex(session('userdata.id'));
        
        usort($settingsIndex->indexes, function($a, $b) { return $b->order - $a->order; });

		$this->tpl->assign('settings', $settingsIndex);
        $this->tpl->assign('exportData', null);
		$this->getEnabledPlugins();
	}

    public function add(){
        $userId = session('userdata.id');
        $settingsIndex = $this->settingsService->GetSettingsIndex($userId);

        $id = 0;
        if (count($settingsIndex->indexes) > 0)
            $id = max(array_map( function($v) { return $v->id; } ,$settingsIndex->indexes)) + 1;
        $settingsIndex->indexes[$id] = new SettingsModel(array());
        $settingsIndex->indexes[$id]->id = $id;

        $this->SetSettingsDataByRequest($settingsIndex, $id);
        $this->settingsService->SetSettingsIndex($settingsIndex, $userId);
        
        $this->tpl->setNotification("Task list added!", 'success');
        $this->get();
    }

    public function save(){
        $userId = session('userdata.id');
        $settingsIndex = $this->settingsService->GetSettingsIndex($userId);
        $this->SetSettingsDataByRequest($settingsIndex, $this->incomingRequest->get("id"));
        $this->ClearCache();
        $this->settingsService->SetSettingsIndex($settingsIndex, $userId);
        
        $this->tpl->setNotification("Task list saved!", 'success');
        $this->get();
    }

    public function delete(){
        $id = $this->incomingRequest->get("id");
        $userId = session('userdata.id');

        $settingsIndex = $this->settingsService->GetSettingsIndex($userId);
        unset($settingsIndex->indexes[$id]);
        $this->settingsService->SetSettingsIndex($settingsIndex, $userId);
        
        $this->tpl->setNotification("Task list deleted!", 'success');
        $this->get();
    }

    public function export(){
        $id = $this->incomingRequest->get("id");

        $settingsIndex = $this->settingsService->GetSettingsIndex(session('userdata.id'));

        $this->get();
        $this->tpl->setNotification("Task list exported!", 'success');
        $this->tpl->assign('exportData', json_encode($settingsIndex->indexes[$id]));
    }

    public function importFile(){
        $userId = session('userdata.id');
        $targetFile = $_FILES['file'];
        $text = file_get_contents($targetFile["tmp_name"]);
        $object = json_decode($text);
        
        $settingsIndex = $this->settingsService->GetSettingsIndex($userId);

        $newId = 0;
        if (count($settingsIndex->indexes) > 0){
            $newId = max(array_column($settingsIndex->indexes, 'id')) + 1;
        }
        $object->id = $newId;

        $settingsIndex->indexes[$object->id] = $object;

        $this->settingsService->SetSettingsIndex($settingsIndex, $userId);

        $this->tpl->setNotification("Task list imported!", 'success');
        $this->get();
    }

    public function import(){
        $text = $this->incomingRequest->get("data");
        $object = json_decode($text);
        $userId = session('userdata.id');

        $settingsIndex = $this->settingsService->GetSettingsIndex($userId);

        $newId = 0;
        if (count($settingsIndex->indexes) > 0){
            $newId = max(array_column($settingsIndex->indexes, 'id')) + 1;
        }
        $object->id = $newId;

        $settingsIndex->indexes[$object->id] = $object;

        $this->settingsService->SetSettingsIndex($settingsIndex, $userId);

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

    private function SetSettingsDataByRequest(SettingsIndex $index, string $id){
        $index->indexes[$id]->name = $this->incomingRequest->get("name");
        $index->indexes[$id]->subtitle = $this->incomingRequest->get("subtitle");

        $index->indexes[$id]->maxtasks = $this->incomingRequest->get("maxtasks");
        $index->indexes[$id]->persistency = $this->incomingRequest->get("persistency");
        $index->indexes[$id]->shownbydefault = null !== ($this->incomingRequest->get("shownbydefault"));
        $index->indexes[$id]->order = $this->incomingRequest->get("order");
        $index->indexes[$id]->hideifempty = null !== ($this->incomingRequest->get("hideifempty"));
        $index->indexes[$id]->showweights = null !== ($this->incomingRequest->get("showweights"));
        $index->indexes[$id]->allowignoring = null !== ($this->incomingRequest->get("allowignoring"));

        $index->indexes[$id]->includetasks = null !== ($this->incomingRequest->get("includetasks"));
        $index->indexes[$id]->includesubtasks = null !== ($this->incomingRequest->get("includesubtasks"));
        $index->indexes[$id]->includebugs = null !== ($this->incomingRequest->get("includebugs"));

        $index->indexes[$id]->modules = json_decode($this->incomingRequest->get("modules"));
    }

    private function ClearCache(){
        $id = $this->incomingRequest->get("id");
        $userId = session('userdata.id');
        $cacheKey = "user.{$userId}.{$id}";
        $this->cacheRepository->deleteCache($cacheKey);
    }
}
