<?php

namespace Leantime\Plugins\TaskOrganiser\Models;

class SettingsIndex
{
    public array $indexes = array();

    public function __construct(string $data) {
        if ($data == "")
        {
            $this->indexes = [];
            $newItem = new SettingsModel();
            $newItem->id = 0;
            $newItem->name = "New Task List";
            $newItem->subtitle = "The default task list";
            $newItem->modules = [];
            array_push($this->indexes, $newItem);
            return;
        }
        $values = json_decode($data);
        $this->indexes = $values->indexes ?? [];
    }

    public function Serialize(){
        return json_encode($this);
    }
}
