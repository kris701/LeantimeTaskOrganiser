<?php

namespace Leantime\Plugins\TaskOrganiser\Services\SortModules;

use Leantime\Plugins\TaskOrganiser\Services\SortModules\BaseSortModule;
use Leantime\Domain\Tickets\Models\Tickets as TicketModel;

use Leantime\Plugins\CustomFields\Services\CustomFields as CustomFieldsService;
use Leantime\Plugins\CustomFields\Repositories\CustomFields as CustomFieldsRepo;
use Leantime\Plugins\CustomFields\Contracts\FieldTypeEnum;

use Leantime\Core\Configuration\Environment;
use Leantime\Core\Db\Db;

class CustomFieldsRadioSortModule extends BaseSortModule
{
    public string $name;
    public array $selectionMap = [];
    private CustomFieldsService $customFieldsService;

    public function __construct(
        Db $db,
        Environment $config,
        $data
    ) {
        $this->customFieldsService = new CustomFieldsService(new CustomFieldsRepo($db), $config);
        $this->name = $data->name;
        $this->selectionMap = get_object_vars($data->selectionMap);
    }

    public function Calculate(TicketModel $ticket) : int{
        $ticketFields = $this->customFieldsService->getAllForTicket($ticket);
        $targetFields = array_filter($ticketFields, function($v){
            return $v->name == $this->name;
        });
        if ($targetFields != null && count($targetFields) > 0){
            $targetField = array_values($targetFields)[0];
            if ($targetField->type != FieldTypeEnum::RADIO)
                return 0;
            if ($targetField->value != ""){
                if (array_key_exists($targetField->value, $this->selectionMap)){
                    $value = $this->selectionMap[$targetField->value];
                    if ($value != null){
                        return $value;
                    }
                }
            }
        }

        return 0;
    }
}
