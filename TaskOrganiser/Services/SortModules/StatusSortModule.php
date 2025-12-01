<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Domain\Projects\Services\Projects as ProjectService;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;

class StatusSortModule extends BaseSortModule
{
    public array $map = [];

    private ProjectService $projectsService;
    private TicketService $ticketService;

    public function __construct(
        $data,
        ProjectService $projectsService,
        TicketService $ticketService
    ) {
        $this->projectsService = $projectsService;
        $this->ticketService = $ticketService;

        $projectMap = get_object_vars($data->map);
        $projects = $this->projectsService->getAllProjects();
        $newMap = [];
        foreach($projects as $project){
            $projectName = $project['name'];
            if (array_key_exists($projectName, $projectMap)){
                $projectId = $project['id'];
                $statusLabels = $ticketService->getStatusLabels($projectId);
                
                $projectStatusMap = get_object_vars($projectMap[$projectName]);
                $newProjectStatusMap = [];
                foreach($statusLabels as $statusIndex => $statusLabel){
                    $statusLabelName = $statusLabel['name'];
                    if (array_key_exists($statusLabelName, $projectStatusMap)){
                        $newProjectStatusMap[$statusIndex] = $projectStatusMap[$statusLabelName];
                    }
                }

                $newMap[$projectId] = $newProjectStatusMap;
            }
        }
        $this->map = $newMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->projectId, $this->map)){
            $statusMap = $this->map[$ticket->projectId];
            if (array_key_exists($ticket->status, $statusMap)){
                $value = $statusMap[$ticket->status];
                if ($value != null){
                    return $value;
                }
            }
        }
        return 0;
    }
}
