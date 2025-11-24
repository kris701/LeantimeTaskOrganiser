<?php

namespace Leantime\Plugins\TaskOrganiser\Models;

class SettingsModel
{
    public int $id;
    public string $name;
    public string $subtitle;
    public $modules = array();
}
