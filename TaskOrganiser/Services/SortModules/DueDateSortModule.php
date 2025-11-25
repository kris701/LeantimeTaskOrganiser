<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

// Config format:
// {
//    "type":"duedate",
//    "daysUntilMap": {
//        "7":1,
//        "5":2,
//        "0":9
//    }
// }
class DueDateSortModule extends BaseSortModule
{
    public array $daysUntilMap = [];

    public function __construct($data) {
        $this->daysUntilMap = get_object_vars($data->daysUntilMap);
        krsort($this->daysUntilMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        if ($ticket->dateToFinish == null || $ticket->dateToFinish == "" || $ticket->dateToFinish == "0000-00-00 00:00:00")
            return 0;

        $utcnow = new \DateTime('now', new \DateTimeZone('UTC'));
        $target = new \DateTime($ticket->dateToFinish);

        $daysUntil = intval(date_diff($utcnow, $target)->format("%r%a"));

        $targetExp = null;
        foreach(array_keys($this->daysUntilMap) as $dayExp){
            if ($dayExp < $daysUntil)
                break;
            $targetExp = $dayExp;
        }
        if ($targetExp != null || $targetExp == 0)
            return $this->daysUntilMap[$targetExp];

        return 0;
    }
}
