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
    public bool $hideifempty;
    public bool $showweights;

    public bool $includetasks;
    public bool $includesubtasks;
    public bool $includebugs;

    public $modules = array();

    public function __construct($data){
        $this->id = $data->id ?? 0;
        $this->name = $data->name ?? '';
        $this->subtitle = $data->subtitle ?? '';

        $this->maxtasks = $data->maxtasks ?? 10;
        $this->persistency = $data->persistency ?? -1;
        $this->shownbydefault = $data->shownbydefault ?? true;
        $this->order = $data->order ?? 0;
        $this->hideifempty = $data->hideifempty ?? false;
        $this->showweights = $data->showweights ?? false;

        $this->includetasks = $data->includetasks ?? true;
        $this->includesubtasks = $data->includesubtasks ?? false;
        $this->includebugs = $data->includebugs ?? false;

        $this->modules = $data->modules ?? [];
    }
}
