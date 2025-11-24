<?php

namespace Leantime\Plugins\TaskOrganiser\Listeners;

use Leantime\Core\UI\Template;

class SettingsTabContent
{
    public function __construct(
        protected Template $template) {}

    public function handle($payload): void
    {
        include __DIR__.'/../Templates/partials/settings.blade.php';
    }
}
