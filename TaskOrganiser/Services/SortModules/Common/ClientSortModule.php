<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Common;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class ClientSortModule extends BaseSortModule
{
    public array $map = [];

    public function __construct($data) {
        $this->map = get_object_vars($data->map);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->clientName, $this->map)){
            $value = $this->map[$ticket->clientName];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
