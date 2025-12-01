<div id="settingsContainer" class="tw-flex tw-flex-col" style="gap:0.5rem">
    <p>Edit your settings for how the Task Organiser widget should work.</p>
    
    <button id="addContainerButton" class="btn btn-outline" style="width:auto !important;">Add</button>
    <div id="addContainer" class="tw-flex tw-flex-col newSettings" style="display:none;gap:0.5rem;">
        <button id="cancelAddContainerButton" class="btn btn-outline" style="width:auto !important;">Cancel</button>
        <form
            hx-post="{{ BASE_URL }}/taskOrganiser/settingsController/add"
            hx-trigger="submit"
            hx-target="#settingsContainer"
            hx-swap="innerhtml"
        >
            <div class="tw-flex tw-flex-col">
                <input name="name" style="width:auto !important;" type="text" placeholder="Name"/>
                <textarea name="subtitle" style="width:auto !important;" placeholder="Description"></textarea>

                <p>General</p>
                <div class="settingField">
                    <p>Max Tasks</p>
                    <input name="maxtasks" type="number" value="10"/>
                </div>
                <div class="settingField">
                    <p>Persistency (hours)</p>
                    <input name="persistency" type="number" value="-1"/>
                </div>
                <div class="settingField">
                    <p>Always Show</p>
                    <input name="shownbydefault" type="checkbox"/>
                </div>
                <div class="settingField">
                    <p>Hide list when empty</p>
                    <input name="hideifempty" type="checkbox"/>
                </div>
                <div class="settingField">
                    <p>Order</p>
                    <input name="order" type="number" value="0"/>
                </div>

                <p>Item Selection</p>
                <div class="settingField">
                    <p>Use Tasks</p>
                    <input name="includetasks" type="checkbox"/>
                </div>
                <div class="settingField">
                    <p>Use SubTasks</p>
                    <input name="includesubtasks" type="checkbox"/>
                </div>
                <div class="settingField">
                    <p>Use bugs</p>
                    <input name="includebugs" type="checkbox"/>
                </div>

                <details>
                    <summary style="height:2rem;align-content:center">
                        Modules
                    </summary>
                    <div style="text-align:center">
                        <p>Here you can configure what sort modules you want to use.</p>
                        <div class="inlineDropDownContainer tw-float-right">
                            <a href="javascript:void(0);" class="dropdown-toggle ticketDropDown editHeadline" data-toggle="dropdown">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <div class="tw-flex tw-flex-col" style="gap:0.5rem;align-items:center;">
                                    <p>Add module definition</p>
                                    @if(array_key_exists('common', $availableplugins) && $availableplugins['common'])
                                        <p>Common</p>
                                        <input type="button" value="Client Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', clientSortModuleDef)"/>
                                        <input type="button" value="Due Date Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', dueDateSortModuleDef)"/>
                                        <input type="button" value="Effort Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', effortSortModuleDef)"/>
                                        <input type="button" value="Top N Effort Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', topNEffortSortModuleDef)"/>
                                        <input type="button" value="Priority Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', prioritySortModuleDef)"/>
                                        <input type="button" value="Status Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', statusSortModuleDef)"/>
                                        <input type="button" value="Project Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', projectSortModuleDef)"/>
                                        <input type="button" value="Milestone Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', milestoneSortModuleDef)"/>
                                        <input type="button" value="Created Within Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', createdWitinSortModuleDef)"/>
                                        <input type="button" value="Type Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', typeSortModuleDef)"/>
                                    @endif
                                    @if(array_key_exists('customfields', $availableplugins) && $availableplugins['customfields'])
                                        <p>Custom Fields</p>
                                        <input type="button" value="Bool Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', customFields_boolDef)"/>
                                        <input type="button" value="Checkbox Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', customFields_checkboxDef)"/>
                                        <input type="button" value="Radio Module" class="btn btn-outline" style="width:80% !important;" onclick="test('newModuleArea', customFields_radioDef)"/>
                                    @endif
                                </div>
                            </ul>
                        </div>
                        <textarea id="newModuleArea" name="modules" class="moduleArea" style="width:80% !important;height:50vh" placeholder="Module Definitions"></textarea>
                    </div>
                </details>
                
                <input type="submit" value="Add" style="width:auto !important;"/>
            </div>
        </form>
    </div>

    <div class="tw-flex tw-flex-col" style="gap:0.5rem">
        @foreach ($settings->indexes as $setting)
            <details class="settingDetails" style="background-color:color-mix(in srgb, var(--secondary-background) 60%, transparent)">
                <summary>{{$setting->name}}</summary>
                <div class="tw-flex tw-flex-col" style="gap:0.5rem;margin-top:10px">
                    <form
                        hx-post="{{ BASE_URL }}/taskOrganiser/settingsController/save"
                        hx-trigger="submit"
                        hx-target="#settingsContainer"
                        hx-swap="innerhtml"
                    >
                        <div class="tw-flex tw-flex-col">
                            <input name="id" type="hidden" value="{{$setting->id}}"/>
                            <input name="name" style="width:auto !important;" type="text" value="{{$setting->name}}"/>
                            <textarea name="subtitle" style="width:auto !important;">{{$setting->subtitle}}</textarea>

                            <p>General</p>
                            <div class="settingField">
                                <p>Max Tasks</p>
                                <input name="maxtasks" type="number" value="{{$setting->maxtasks}}"/>
                            </div>
                            <div class="settingField">
                                <p>Persistency (hours)</p>
                                <input name="persistency" type="number" value="{{$setting->persistency}}"/>
                            </div>
                            <div class="settingField">
                                <p>Always Show</p>
                                @if($setting->shownbydefault)
                                    <input name="shownbydefault" type="checkbox" checked value="true"/>
                                @else
                                    <input name="shownbydefault" type="checkbox"/>
                                @endif
                            </div>
                            <div class="settingField">
                                <p>Hide list when empty</p>
                                @if($setting->hideifempty)
                                    <input name="hideifempty" type="checkbox" checked value="true"/>
                                @else
                                    <input name="hideifempty" type="checkbox"/>
                                @endif
                            </div>
                            <div class="settingField">
                                <p>Order</p>
                                <input name="order" type="number" value="{{$setting->order}}"/>
                            </div>

                            <p>Item Selection</p>
                            <div class="settingField">
                                <p>Use Tasks</p>
                                @if($setting->includetasks)
                                    <input name="includetasks" type="checkbox" checked value="true"/>
                                @else
                                    <input name="includetasks" type="checkbox"/>
                                @endif
                            </div>
                            <div class="settingField">
                                <p>Use SubTasks</p>
                                @if($setting->includesubtasks)
                                    <input name="includesubtasks" type="checkbox" checked value="true"/>
                                @else
                                    <input name="includesubtasks" type="checkbox"/>
                                @endif
                            </div>
                            <div class="settingField">
                                <p>Use Bugs</p>
                                @if($setting->includebugs)
                                    <input name="includebugs" type="checkbox" checked value="true"/>
                                @else
                                    <input name="includebugs" type="checkbox"/>
                                @endif
                            </div>

                            <details>
                                <summary style="height:2rem;align-content:center">
                                    Modules
                                </summary>
                                <div style="text-align:center">
                                    <p>Here you can configure what sort modules you want to use.</p>
                                    <div class="inlineDropDownContainer tw-float-right">
                                        <a href="javascript:void(0);" class="dropdown-toggle ticketDropDown editHeadline" data-toggle="dropdown">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </a>
                                        <ul class="dropdown-menu">
                                            <div class="tw-flex tw-flex-col" style="gap:0.5rem;align-items:center;">
                                                <p>Add module definition</p>
                                                @if(array_key_exists('common', $availableplugins) && $availableplugins['common'])
                                                    <p>Common</p>
                                                    <input type="button" value="Client Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', clientSortModuleDef)"/>
                                                    <input type="button" value="Due Date Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', dueDateSortModuleDef)"/>
                                                    <input type="button" value="Effort Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', effortSortModuleDef)"/>
                                                    <input type="button" value="Top N Effort Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', topNEffortSortModuleDef)"/>
                                                    <input type="button" value="Priority Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', prioritySortModuleDef)"/>
                                                    <input type="button" value="Status Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', statusSortModuleDef)"/>
                                                    <input type="button" value="Project Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', projectSortModuleDef)"/>
                                                    <input type="button" value="Milestone Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', milestoneSortModuleDef)"/>
                                                    <input type="button" value="Created Witin Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', createdWitinSortModuleDef)"/>
                                                    <input type="button" value="Type Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', typeSortModuleDef)"/>
                                                @endif
                                                @if(array_key_exists('customfields', $availableplugins) && $availableplugins['customfields'])
                                                    <p>Custom Fields</p>
                                                    <input type="button" value="Bool Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', customFields_boolDef)"/>
                                                    <input type="button" value="Checkbox Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', customFields_checkboxDef)"/>
                                                    <input type="button" value="Radio Module" class="btn btn-outline" style="width:80% !important;" onclick="test('modulearea-{{$setting->id}}', customFields_radioDef)"/>
                                                @endif
                                            </div>
                                        </ul>
                                    </div>
                                    <textarea id="modulearea-{{$setting->id}}" name="modules" class="moduleArea" style="width:80% !important;height:50vh" placeholder="Module Definitions">{{json_encode($setting->modules)}}</textarea>
                                </div>
                            </details>

                            <input type="submit" value="Save" style="width:auto !important;"/>
                        </div>
                    </form>
                    <form
                        hx-delete="{{ BASE_URL }}/taskOrganiser/settingsController/delete"
                        hx-trigger="submit"
                        hx-target="#settingsContainer"
                        hx-swap="innerhtml"
                    >
                        <div class="tw-flex tw-flex-col">
                            <input name="id" type="hidden" value="{{$setting->id}}"/>
                            <input type="submit" value="Delete" style="width:auto !important;"/>
                        </div>
                    </form>
                </div>
            </details>
        @endforeach
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        var items = document.querySelectorAll(".moduleArea")
        items.forEach(x => {
            if (x.value && x.value != ""){
                var object = JSON.parse(x.value);
                var pretty = JSON.stringify(object, undefined, 4);
                x.value = pretty;
            }
        })

        document.getElementById('addContainerButton').addEventListener('click', function () {
            jQuery("#addContainer").show();
        });
        document.getElementById('cancelAddContainerButton').addEventListener('click', function () {
            jQuery("#addContainer").hide();
        });
    });

    function test(elementName, target){
        var element = document.getElementById(elementName);
        var text = element.value;
        if (text == null || text == "")
            text = "[]"
        var object = JSON.parse(text);
        object.push(target);
        element.value = JSON.stringify(object, undefined, 4);
    }

    // Common
    clientSortModuleDef = {
        "type":"client",
        "map":{
            "client_name":0
        }
    }
    dueDateSortModuleDef = {
        "type":"duedate",
        "map":{
            "7":1,
            "2":5
        }
    }
    effortSortModuleDef = {
        "type":"effort",
        "map":{
            "XS":5,
            "S":1
        }
    }
    topNEffortSortModuleDef = {
        "type":"topneffort",
        "top": 2,
        "map":{
            "XS":5,
            "S":1
        }
    }
    prioritySortModuleDef = {
        "type":"priority",
        "map":{
            "priority_name":0
        }
    }
    statusSortModuleDef = {
        "type":"status",
        "map":{
            "project_name":{
                "status_name":0
            }
        }
    }
    projectSortModuleDef = {
        "type":"project",
        "map":{
            "project_name":0
        }
    }
    milestoneSortModuleDef = {
        "type":"milestone",
        "map":{
            "milestone_name":0
        }
    }
    createdWitinSortModuleDef = {
        "type":"createdwithin",
        "hours": 10,
        "weight": 100
    }
    typeSortModuleDef = {
        "type":"type",
        "map":{
            "type_name":0
        }
    }

    // Custom Fields
    customFields_boolDef = {
        "type":"customfields_bool",
        "name":"field_name",
        "trueWeight":0,
        "falseWeight":0
    }
    customFields_checkboxDef = {
        "type":"customfields_checkbox",
        "name":"field_name",
        "map":{
            "value":0
        }
    }
    customFields_radioDef = {
        "type":"customfields_radio",
        "name":"field_name",
        "map":{
            "value":0
        }
    }

</script>

<style>
    .settingField {
        display:flex;
        flex-direction: row;
        border: 1px solid var(--main-border-color);
        border-radius: var(--box-radius);
        box-shadow: var(--regular-shadow);
        align-items: center;
        text-align: center;
        margin-top: 5px;
        margin-bottom: 5px;

        > p {
            width:15%;
        }

        > input {
            width:100%;
            margin:2px
        }
    }

    .newSettings {
        background-color: var(--secondary-background);
        border: 3px solid transparent;
        border-bottom: none;
        border-radius: var(--box-radius);
        border-top: none;
        box-shadow: var(--regular-shadow);
        margin-bottom: 10px;
        padding: 10px;
    }

    .settingDetails {
        background-color: var(--secondary-background);
        border: 3px solid transparent;
        border-bottom: none;
        border-radius: var(--box-radius);
        border-top: none;
        box-shadow: var(--regular-shadow);
        margin-bottom: 10px;
        padding: 10px;
    }

    .settingDetails summary {
        cursor: pointer;
    }
</style>