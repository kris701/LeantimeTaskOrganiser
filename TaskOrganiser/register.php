<?php

function addTaskOrganiserLink($menuStructure, $params)
{    
    return $menuStructure;
}
\Leantime\Core\Events\EventDispatcher::add_filter_listener('leantime.domain.menu.repositories.menu.getMenuStructure.menuStructures', 'addTaskOrganiserLink');

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
\Leantime\Core\Events\EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.availableWidgets', 'addTaskOrganiserWidget');

function addDefaultTaskOrganiserWidget($defaultWidgets, $params)
{
    $moduleManager = app()->make(\Leantime\Domain\Modulemanager\Services\Modulemanager::class);
    if ($moduleManager->isModuleAvailable('taskOrganiser')) {

        $defaultWidgets['taskOrganiser'] = $params['availableWidgets']['taskOrganiser'];
    }

    return $defaultWidgets;
}

\Leantime\Core\Events\EventDispatcher::add_filter_listener('leantime.domain.widgets.services.widgets.__construct.defaultWidgets', 'addDefaultTaskOrganiserWidget');
