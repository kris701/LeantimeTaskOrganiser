<div id="settingsContainer">
    <p>Edit your settings for how the Task Organiser widget should work.</p>
    
    @foreach ($settings->indexes as $setting)
    <details>
        <summary>{{$setting->name}}</summary>
        <form
            hx-post="{{ BASE_URL }}/taskOrganiser/settingsController/save"
            hx-trigger="submit"
            hx-target="#settingsContainer"
            hx-swap="innerhtml"
        >
            <div class="tw-flex tw-flex-col">
                <input name="id" type="hidden" value="{{$setting->id}}"/>
                <input name="name" type="text" value="{{$setting->name}}"/>
                <textarea name="subtitle">{{$setting->subtitle}}</textarea>

                <p>Modules</p>
                <textarea name="modules">{{json_encode($setting->modules)}}</textarea>

                <input type="submit" value="Save" name="submitBtn" style="width:auto !important;"/>
            </div>
        </form>
    </details>
    @endforeach
</div>

<script>


</script>
