<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class EffortSortModule extends BaseSortModule
{
    public array $effortMap = [];

    public function __construct($data) {
        $this->effortMap = get_object_vars($data->effortMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->storypoints, $this->effortMap)){
            $effortValue = $this->effortMap[$ticket->storypoints];
            if ($effortValue != null){
                return $effortValue;
            }
        }
        return 0;
    }
}
