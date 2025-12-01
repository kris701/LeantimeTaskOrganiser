<?php

namespace Leantime\Plugins\TaskOrganiser\Models;

use Leantime\Plugins\TaskOrganiser\Models\SettingsModel;

class SettingsIndex
{
    public array $indexes = array();

    public function __construct(string $data) {
        if ($data == "")
        {
            $this->indexes = [];
            $newItem = new SettingsModel(array());
            $newItem->id = 0;
            $newItem->name = "New Task List";
            $newItem->subtitle = "The default task list";

            $newItem->maxtasks = 10;
            $newItem->persistency = -1;
            $newItem->shownbydefault = true;
            $newItem->order = 0;
            $newItem->hideifempty = false;

            $newItem->includetasks = true;
            $newItem->includesubtasks = true;
            $newItem->includebugs = false;

            $newItem->modules = array();
            array_push($this->indexes, $newItem);
            return;
        }
        $values = json_decode($data);
        $this->indexes = [];
        if ($values->indexes != null){
            foreach($values->indexes as $index){
                array_push($this->indexes, new SettingsModel($index));
            }
        }
    }

    public function Serialize(){
        return json_encode($this);
    }
}
