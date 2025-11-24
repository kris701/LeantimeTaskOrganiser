<div>
    <p>Edit your settings for how the Task Organiser widget should work.</p>
    @foreach ($projects as $project)
        <div>
            <details>
                <summary>Settings for {{$project['name']}}</summary>
                <form
                    hx-post="{{ BASE_URL }}/taskOrganiser/settingsController/save"
                    hx-trigger="submit"
                    hx-swap="none"
                >
                    <input type="hidden" value="{{ $project['id'] }}" name="project"/>

                    <p>Global Weight</p>
                    <input type="number" name="globalWeight" value="{{ $settings[$project['id']]->globalWeight ?? 1 }}" class="main-title-input tw-w-full" placeholder="Global Weight" style="background:none;"/><br />
                    
                    <input type="submit" value="Save" style="width:auto !important;"/>
                </form>
            </details>
        </div>
    @endforeach
</div>
