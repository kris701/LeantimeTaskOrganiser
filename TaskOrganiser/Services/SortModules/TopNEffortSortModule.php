<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class TopNEffortSortModule extends BaseSortModule
{
    public array $effortMap = [];
    public int $top;

    private int $processed = 0;

    public function __construct($data) {
        $this->effortMap = get_object_vars($data->effortMap);
        $this->top = $data->top;
    }

    public function Calculate(TicketModel $ticket) : int{
        if ($this->processed >= $this->top)
            return 0;
        if (array_key_exists($ticket->storypoints, $this->effortMap)){
            $this->processed++;
            $effortValue = $this->effortMap[$ticket->storypoints];
            if ($effortValue != null){
                return $effortValue;
            }
        }
        return 0;
    }
}
