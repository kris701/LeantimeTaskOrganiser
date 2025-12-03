<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Common;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Domain\Projects\Services\Projects as ProjectService;

class ProjectSortModule extends BaseSortModule
{
    public array $map = [];

    private ProjectService $projectsService;

    public function __construct(
        $data,
        ProjectService $projectsService
    ) {
        $this->projectsService = $projectsService;

        $projectMap = get_object_vars($data->map);
        $projects = $this->projectsService->getAllProjects();
        $newMap = [];
        foreach($projects as $project){
            if (array_key_exists($project['name'], $projectMap))
                $newMap[$project['id']] = $projectMap[$project['name']];
        }
        $this->map = $newMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->projectId, $this->map)){
            $value = $this->map[$ticket->projectId];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
