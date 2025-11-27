<?php

namespace Leantime\Plugins\TaskOrganiser\Models;

class SettingsModel
{
    public int $id;
    public string $name;
    public string $subtitle;

    public int $maxtasks;
    public int $persistency;
    public bool $shownbydefault;
    public int $order;

    public bool $includetasks;
    public bool $includesubtasks;

    public $modules = array();
}
