<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class ClientSortModule extends BaseSortModule
{
    public array $clientMap = [];

    public function __construct($data) {
        $this->clientMap = get_object_vars($data->clientMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->clientName, $this->clientMap)){
            $clientNameValue = $this->clientMap[$ticket->clientName];
            if ($clientNameValue != null){
                return $clientNameValue;
            }
        }
        return 0;
    }
}
