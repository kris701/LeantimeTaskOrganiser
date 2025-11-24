<?php

namespace Leantime\Plugins\TaskOrganiser\Models;

class SettingsModel
{
    public int $globalWeight = 1;
    public int $todaysWeight = 1;

    public $modules = array();

    public function __construct(string $data) {
        if ($data == "")
            return;
        $values = json_decode($data);
        $this->globalWeight = $values->globalWeight ?? 1;
        $this->todaysWeight = $values->todaysWeight ?? 1;
        $this->modules = $values->modules ?? [];
    }

    public function Serialize(){
        return json_encode($this);
    }
}
