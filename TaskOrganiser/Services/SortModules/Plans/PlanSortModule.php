<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules\Plans;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

use Leantime\Plugins\PgmPro\Repositories\Programs as PlansRepository;

use Leantime\Core\Configuration\Environment;
use Leantime\Core\Db\Db;
use Leantime\Core\Language;

class PlanSortModule extends BaseSortModule
{
    public array $map = [];
    private PlansRepository $plansRepository;

    public function __construct(
        Db $db,
        Environment $config,
        Language $language,
        $data
    ) {
        $this->plansRepository = new PlansRepository(
            $config,
            $db,
            $language
        );

        $strategyMap = get_object_vars($data->map);
        $projects = $this->plansRepository->getAll();        
        $newStrategyMap = [];
        foreach($projects as $project){
            if ($project["menuType"] != "program")
                continue;
            if (array_key_exists($project['name'], $strategyMap))
                $newStrategyMap[$project['id']] = $strategyMap[$project['name']];
        }
        $newMap = [];
        foreach($projects as $project){
            $strategyParent = $this->FindStrategyParentOrNull($project, $projects, $newStrategyMap);
            if ($strategyParent != null)
                $newMap[$project['id']] = $newStrategyMap[$strategyParent];
        }
        $this->map = $newMap;
    }

    private function FindStrategyParentOrNull($project, $projects, $newStrategyMap) {
        if (array_key_exists($project['parentId'], $newStrategyMap))
            return $project['parentId'];

        foreach($projects as $subProject)
            if ($project['parentId'] == $subProject['id'])
                return $this->FindStrategyParentOrNull($subProject, $projects, $newStrategyMap);

        return null;
    }

    public function Calculate(TicketModel $ticket) : int{
        if (array_key_exists($ticket->projectId, $this->map)){
            $value = $this->map[$ticket->projectId];
            if ($value != null){
                return $value;
            }
        }
        return 0;
    }
}
