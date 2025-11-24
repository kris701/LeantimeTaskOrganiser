<?php

namespace Leantime\Plugins\TaskOrganiser\Listeners;

class SettingsTab
{
    public function handle($payload): void
    {
        echo '<li><a href="#organisertab"><i class="fa-solid fa-sort"></i> Task Organiser</a></li>';
    }
}
