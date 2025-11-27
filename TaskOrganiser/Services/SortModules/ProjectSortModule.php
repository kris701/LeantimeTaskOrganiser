<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class ProjectSortModule extends BaseSortModule
{
    public array $projectMap = [];

    public function __construct($data) {
        $this->projectMap = get_object_vars($data->projectMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->projectId, $this->projectMap)){
            $projectValue = $this->projectMap[$ticket->projectId];
            if ($projectValue != null){
                return $projectValue;
            }
        }
        return 0;
    }
}
