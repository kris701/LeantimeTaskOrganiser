<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Common;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;
use Leantime\Domain\Tickets\Services\Tickets as TicketService;
use Leantime\Domain\Projects\Services\Projects as ProjectService;

class MilestoneSortModule extends BaseSortModule
{
    public array $map = [];

    private ProjectService $projectsService;
    private TicketService $ticketsService;

    public function __construct(
        $data,
        TicketService $ticketsService,
        ProjectService $projectsService
    ) {
        $this->ticketsService = $ticketsService;
        $this->projectsService = $projectsService;

        $newMap = [];
        $milestoneMap = get_object_vars($data->map);
        
        $projects = $this->projectsService->getAllProjects();
        foreach($projects as $project){
            $milestones = $this->ticketsService->getAllMilestones(["type"=>"milestone", "currentProject"=>$project['id']]);
            foreach($milestones as $milestone){
                if (array_key_exists($milestone->headline, $milestoneMap))
                    $newMap[$milestone->id] = $milestoneMap[$milestone->headline];
            }
        }
        $this->map = $newMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->milestoneid, $this->map)){
            $value = $this->map[$ticket->milestoneid];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
