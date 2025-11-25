<?php

namespace Leantime\Plugins\TaskOrganiser\Repositories;

use Leantime\Plugins\TaskOrganiser\Models\CachedTaskList;
use Leantime\Core\Db\Db as DbCore;
use PDO;

class CacheRepository
{
    public function __construct(
        protected DbCore $db,
    ) {
        $this->db = $db;
    }

    /**
     * @return bool
     */
    public function doesKeyExist(string $key): bool
    {
        $sql = "SELECT COUNT(*) FROM zp_taskorganisercache WHERE zp_taskorganisercache.id = :key";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':key', $key, PDO::PARAM_STR);

        $stmn->execute();
        $result = $stmn->fetch();
        $stmn->closeCursor();

        return $result == 1;
    }

    /**
     * @return CachedTaskList|false
     */
    public function getCache(string $key): CachedTaskList | false
    {
        $sql = "SELECT zp_taskorganisercache.id, zp_taskorganisercache.expires, zp_taskorganisercache.tasklist FROM zp_taskorganisercache WHERE zp_taskorganisercache.id = :key";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':key', $key, PDO::PARAM_STR);

        $stmn->execute();
        $result = $stmn->fetchObject(CachedTaskList::class);
        $stmn->closeCursor();

        return $result;
    }

    public function deleteCache(string $id)
    {
        $sql = "DELETE FROM zp_taskorganisercache WHERE zp_taskorganisercache.id = :id;";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $id, PDO::PARAM_STR);

        $stmn->execute();
        $stmn->closeCursor();
    }

    public function addCache(CachedTaskList $value)
    {
        $sql = "DELETE FROM zp_taskorganisercache WHERE zp_taskorganisercache.id = :id; INSERT INTO zp_taskorganisercache VALUES (:id, :expires, :tasklist)";

        $stmn = $this->db->database->prepare($sql);
        $stmn->bindValue(':id', $value->id, PDO::PARAM_STR);
        $stmn->bindValue(':expires', $value->expires, PDO::PARAM_STR);
        $stmn->bindValue(':tasklist', $value->tasklist, PDO::PARAM_STR);

        $stmn->execute();
        $stmn->closeCursor();
    }
}
