<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class StatusSortModule extends BaseSortModule
{
    public array $statusMap = [];

    public function __construct($data) {
        $this->statusMap = get_object_vars($data->statusMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->status, $this->statusMap)){
            $statusValue = $this->statusMap[$ticket->status];
            if ($statusValue != null){
                return $statusValue;
            }
        }
        return 0;
    }
}
