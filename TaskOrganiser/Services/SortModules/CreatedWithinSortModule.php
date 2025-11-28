<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class CreatedWithinSortModule extends BaseSortModule
{
    public int $hours;
    public int $weight;

    public function __construct($data) {
        $this->hours = $data->hours;
        $this->weight = $data->weight;
    }

    public function Calculate(TicketModel $ticket) : int{
        if ($ticket->date == null || $ticket->date == "" || $ticket->date == "0000-00-00 00:00:00")
            return 0;

        $utcnow = new \DateTime('now', new \DateTimeZone('UTC'));
        $target = new \DateTime($ticket->date);

        $diff = date_diff($utcnow, $target);
        $hoursFrom = intval($diff->h + ($diff->days * 24));

        var_dump($hoursFrom);

        if ($hoursFrom <= $this->hours)
            return $this->weight;

        return 0;
    }
}
