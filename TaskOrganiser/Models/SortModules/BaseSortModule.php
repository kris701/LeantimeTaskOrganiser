<?php

namespace Leantime\Plugins\TaskOrganiser\Models\SortModules;

use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

abstract class BaseSortModule
{
    public abstract function Calculate(TicketModel $ticket) : int;
}
