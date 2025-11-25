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

                <p>Modules</p>
                <textarea name="modules" class="moduleArea" style="width:auto !important;height:20vh" placeholder="Module Definitions"></textarea>

                <input type="submit" value="Add" style="width:auto !important;"/>
            </div>
        </form>
    </div>

    <div class="tw-flex tw-flex-col" style="gap:0.5rem">
        @foreach ($settings->indexes as $setting)
            <details class="settingDetails">
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

                            <p>Modules</p>
                            <textarea name="modules" class="moduleArea" style="width:auto !important;height:20vh">{{json_encode($setting->modules)}}</textarea>

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