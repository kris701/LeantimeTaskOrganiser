<?php

use Leantime\Core\Events\EventDispatcher;
use Leantime\Plugins\TaskOrganiser\Listeners\TaskOrganiserSettingsTab;
use Leantime\Plugins\TaskOrganiser\Listeners\TaskOrganiserSettingsTabContent;

function addTaskOrganiserLink($menuStructure, $params)
{    
    return $menuStructure;
}
EventDispatcher::add_filter_listener('leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures', 'addTaskOrganiserLink');

function addTaskOrganiserWidget($availableWidgets)
{
    $moduleManager = app()->make(\Leantime\Domain\Modulemanager\Services\Modulemanager::class);
    if ($moduleManager->isModuleAvailable('taskOrganiser')) {

        $availableWidgets['taskOrganiser'] = app()->make("Leantime\Domain\Widgets\Models\Widget", [
            'id' => 'taskOrganiser',
            'name' => 'Task Organiser',
            'description' => 'Simple widget to order your todo items!',
            'gridHeight' => 10,
            'gridWidth' => 10,
            'gridMinHeight' => 10,
            'gridMinWidth' => 10,
            'gridX' => 8,
            'gridY' => 45,
            'alwaysVisible' => false,
            'widgetUrl' => BASE_URL.'/taskOrganiser/taskOrganiserWidget/get',
        ]);
    }

    return $availableWidgets;
}
EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.availableWidgets', 'addTaskOrganiserWidget');

function addDefaultTaskOrganiserWidget($defaultWidgets, $params)
{
    $moduleManager = app()->make(\Leantime\Domain\Modulemanager\Services\Modulemanager::class);
    if ($moduleManager->isModuleAvailable('taskOrganiser')) {

        $defaultWidgets['taskOrganiser'] = $params['availableWidgets']['taskOrganiser'];
    }

    return $defaultWidgets;
}

EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.defaultWidgets', 'addDefaultTaskOrganiserWidget');

EventDispatcher::add_event_listener('leantime.domain.users.templates.editOwn.tabs', TaskOrganiserSettingsTab::class);
EventDispatcher::add_event_listener('leantime.domain.users.templates.editOwn.tabsContent', TaskOrganiserSettingsTabContent::class);
