<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

use Leantime\Plugins\CustomFields\Services\CustomFields as CustomFieldsService;
use Leantime\Plugins\CustomFields\Repositories\CustomFields as CustomFieldsRepo;
use Leantime\Plugins\CustomFields\Contracts\FieldTypeEnum;

use Leantime\Core\Configuration\Environment;
use Leantime\Core\Db\Db;

class CustomFieldsCheckboxSortModule extends BaseSortModule
{
    public string $name;
    public array $map = [];
    private CustomFieldsService $customFieldsService;

    public function __construct(
        Db $db,
        Environment $config,
        $data
    ) {
        $this->customFieldsService = new CustomFieldsService(new CustomFieldsRepo($db), $config);
        $this->name = $data->name;
        $this->map = get_object_vars($data->map);
    }

    public function Calculate(TicketModel $ticket) : int{
        $ticketFields = $this->customFieldsService->getAllForTicket($ticket);
        $targetFields = array_filter($ticketFields, function($v){
            return $v->name == $this->name;
        });
        if ($targetFields != null && count($targetFields) > 0){
            $targetField = array_values($targetFields)[0];
            if ($targetField->type != FieldTypeEnum::CHECKBOX)
                return 0;

            if ($targetField->value != ""){
                $totalValue = 0;
                foreach($targetField->value as $value){
                    if (array_key_exists($value, $this->map)){
                        $value = $this->map[$value];
                        if ($value != null){
                            $totalValue += $value;
                        }
                    }
                }

                return $totalValue;
            }
        }

        return 0;
    }
}
