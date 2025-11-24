<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Models\ModuleSettings;
use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class StatusSortModule extends BaseSortModule
{
    public array $statusMap = [];

    public function __construct(ModuleSettings $data) {
        $this->statusMap = $data->statusMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        $statusValue = $this->statusMap[$ticket->status];
        if ($statusValue != null)
            return $statusValue;
        return 0;
    }
}
