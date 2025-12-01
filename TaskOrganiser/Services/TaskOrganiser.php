<?php

namespace Leantime\Plugins\TaskOrganiser\Services;

use Leantime\Core\Db\Db as DbCore;
use Leantime\Domain\Setting\Services\Setting as SettingService;
use Leantime\Domain\Users\Services\Users as UserService;

class TaskOrganiser
{
    private SettingService $settingsService;
    private UserService $userService;

    public function __construct(
        protected DbCore $db,
        SettingService $settingsService,
        UserService $userService
    ) {
        $this->db = $db;
        $this->settingsService = $settingsService;
        $this->userService = $userService;
    }

    public function install() {
        // Remove all old settings
        $users = $this->userService->getAll();
        foreach($users as $user){
            $sortingKey = "user.{$user['id']}.taskorganisersettings";
            $this->settingsService->deleteSetting($sortingKey);
        }

        // Remake the cache
        $sql = "DROP TABLE IF EXISTS leantime.zp_taskorganisercache; CREATE TABLE leantime.zp_taskorganisercache (
            id varchar(255),
            expires varchar(255),
            tasklist LONGTEXT
        );";

        $stmn = $this->db->database->prepare($sql);
        $stmn->execute();
        $stmn->closeCursor();
    }

    public function update() {}
}
