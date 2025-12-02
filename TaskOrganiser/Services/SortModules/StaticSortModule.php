<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class StaticSortModule extends BaseSortModule
{
    public int $weight;

    public function __construct($data) {
        $this->map = $data->weight;
    }

    public function Calculate(TicketModel $ticket) : int{
        return $this->weight;
    }
}
