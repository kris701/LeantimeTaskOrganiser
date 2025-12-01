<?php

namespace Leantime\Plugins\TaskOrganiser\Services;
use Leantime\Core\Db\Db as DbCore;

class TaskOrganiser
{
    public function __construct(
        protected DbCore $db,
    ) {
        $this->db = $db;
    }

    public function install() {
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
