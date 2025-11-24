<?php

use Leantime\Core\Events\EventDispatcher;
use Leantime\Plugins\TaskOrganiser\Listeners\SettingsTab;
use Leantime\Plugins\TaskOrganiser\Listeners\SettingsTabContent;

function addWidget($availableWidgets)
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
            'widgetUrl' => BASE_URL.'/taskOrganiser/widgetController/get',
        ]);
    }

    return $availableWidgets;
}
EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.availableWidgets', 'addWidget');

function addDefaultWidget($defaultWidgets, $params)
{
    $moduleManager = app()->make(\Leantime\Domain\Modulemanager\Services\Modulemanager::class);
    if ($moduleManager->isModuleAvailable('taskOrganiser')) {

        $defaultWidgets['taskOrganiser'] = $params['availableWidgets']['taskOrganiser'];
    }

    return $defaultWidgets;
}
EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.defaultWidgets', 'addDefaultWidget');

EventDispatcher::add_event_listener('leantime.domain.users.templates.editOwn.tabs', SettingsTab::class);
EventDispatcher::add_event_listener('leantime.domain.users.templates.editOwn.tabsContent', SettingsTabContent::class);
