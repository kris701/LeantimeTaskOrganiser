<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class TopNEffortSortModule extends BaseSortModule
{
    public array $map = [];
    public int $top;

    private int $processed = 0;

    public function __construct($data) {
        $this->map = get_object_vars($data->map);
        $this->top = $data->top;
    }

    public function Calculate(TicketModel $ticket) : int{
        if ($this->processed >= $this->top)
            return 0;
        if (array_key_exists($ticket->storypoints, $this->map)){
            $this->processed++;
            $value = $this->map[$ticket->storypoints];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
