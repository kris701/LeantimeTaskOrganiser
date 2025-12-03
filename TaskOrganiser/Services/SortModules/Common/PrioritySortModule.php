<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Common;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class PrioritySortModule extends BaseSortModule
{
    public array $map = [];

    public function __construct($data) {
        $this->map = get_object_vars($data->map);
        $keys = array_keys($this->map);
        $newMap = [];
        foreach($keys as $key){
            if ($key == "Critical")
                $newMap["1"] = $this->map[$key];
            if ($key == "High")
                $newMap["2"] = $this->map[$key];
            if ($key == "Medium")
                $newMap["3"] = $this->map[$key];
            if ($key == "Low")
                $newMap["5"] = $this->map[$key];
            if ($key == "Lowest")
                $newMap["8"] = $this->map[$key];
        }
        $this->map = $newMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->priority, $this->map)){
            $value = $this->map[$ticket->priority];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
