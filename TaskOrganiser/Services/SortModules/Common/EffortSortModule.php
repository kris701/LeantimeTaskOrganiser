<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Common;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

class EffortSortModule extends BaseSortModule
{
    public array $map = [];

    public function __construct($data) {
        $this->map = get_object_vars($data->map);
        $keys = array_keys($this->map);
        $newMap = [];
        foreach($keys as $key){
            if ($key == "XS")
                $newMap[1] = $this->map[$key];
            if ($key == "S")
                $newMap[2] = $this->map[$key];
            if ($key == "M")
                $newMap[3] = $this->map[$key];
            if ($key == "L")
                $newMap[5] = $this->map[$key];
            if ($key == "XL")
                $newMap[8] = $this->map[$key];
            if ($key == "XXL")
                $newMap[13] = $this->map[$key];
        }
        $this->map = $newMap;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->storypoints, $this->map)){
            $value = $this->map[$ticket->storypoints];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
