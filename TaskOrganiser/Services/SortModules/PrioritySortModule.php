<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class PrioritySortModule extends BaseSortModule
{
    public array $priorityMap = [];

    public function __construct($data) {
        $this->priorityMap = get_object_vars($data->priorityMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->priority, $this->priorityMap)){
            $priorityValue = $this->priorityMap[$ticket->priority];
            if ($priorityValue != null){
                return $priorityValue;
            }
        }
        return 0;
    }
}
