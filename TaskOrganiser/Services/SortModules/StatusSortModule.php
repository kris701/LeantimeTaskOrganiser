<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class StatusSortModule extends BaseSortModule
{
    public array $map = [];

    public function __construct($data) {
        $this->map = get_object_vars($data->map);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->status, $this->map)){
            $value = $this->map[$ticket->status];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
