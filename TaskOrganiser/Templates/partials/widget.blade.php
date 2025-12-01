<div id="taskOrganiserBody"
	class="tw-w-full tw-h-full tw-overflow-x-hidden"
>
	@foreach ($settings as $setting)
		<details class="taskDetails" style="background-color:color-mix(in srgb, var(--secondary-background) 60%, transparent)" {{$setting->shownbydefault ? 'open' : ''}}>
			<summary>{{$setting->name}}
			<div class="inlineDropDownContainer tw-float-right">
				<a href="javascript:void(0);" class="dropdown-toggle ticketDropDown editHeadline" data-toggle="dropdown">
					<i class="fa fa-ellipsis-v" aria-hidden="true"></i>
				</a>
				<ul class="dropdown-menu">
					<form
						hx-delete="{{ BASE_URL }}/taskOrganiser/widgetController/clearCache"
						hx-trigger="submit"
						hx-target="#taskOrganiserBody"
						hx-swap="innerhtml"
					>
						<div class="tw-flex tw-flex-col">
							<input name="id" type="hidden" value="{{$setting->id}}"/>
							<input type="submit" value="Force Recalculate" style="width:50%;align-self:center;"/>
						</div>
					</form>
					<div class="tw-flex tw-flex-col" style="margin:5px;align-items:center">
						<p>Will be recalculated after</p>
						<p id="expiration-{{$setting->id}}">Loading...</p>
					</div>
				</ul>
			</div>
			</summary>
			<div class="tw-flex tw-flex-col" style="gap:0.5rem;margin-top:10px">
				<p>{{$setting->subtitle}}</p>

				@if(count($tasks[$setting->id]) == 0)
					<p style="align-self:center;font-size:20px">🥳 You have no more tickets for this list!</p>
				@else
					<div class="ticketBox" style="cursor:default">
						<div class="tw-flex tw-flex-row" style="gap:1rem">
							<p style="align-content:center;text-align:center;width:3rem">Weight</p>
							<p style="align-content:center;text-align:center;width:3rem">ID</p>
							<p class="tw-flex-1">Name and Project</p>
							<p style="align-content:center;text-align:center;width:10rem">Priority</p>
							<p style="align-content:center;text-align:center;width:10rem">Effort</p>
							<p style="align-content:center;text-align:center;width:10rem">Status</p>
						</div>
					</div>
					<div id="ticketOrganiserContainer">
						@foreach ($tasks[$setting->id] as $ticket)
							<div class="ticketBox" style="cursor:default">
								<div class="tw-flex tw-flex-row" style="gap:1rem">
									<div style="align-content:center;text-align:center;width:3rem">
										<span>{{ $ticket->weight }}</span>
									</div>	

									<div style="align-content:center;text-align:center;width:3rem">
										<span>#{{ $ticket->id }}</span>
									</div>

									<div class="tw-flex-1 ticket-title ticket-title-wrapper">
										<div class="title-text">
											<small style="display:inline-block; ">{{ $ticket->projectName }}</small> <br/>
											<strong><a href="#/tickets/showTicket/{{ $ticket->id }}" preload="mouseover"
													class="ticket-headline-{{ $ticket->id }}">{{ $ticket->headline }}</a></strong>
										</div>
									</div>
									
									<div
										style="align-content:center;text-align:center;width:10rem">
										<span class="text">
											@if(isset($priorityLabels[$ticket->priority]))
												{{ $priorityLabels[$ticket->priority] }}
											@else
												No priority set
											@endif
										</span>
									</div>

									<div
										style="align-content:center;text-align:center;width:10rem">
										<span class="text">
											@if(isset($effortLabels[$ticket->storypoints]))
												{{ $effortLabels[$ticket->storypoints] }}
											@else
												No effort set
											@endif
										</span>
									</div>

									<div
										class="tw-flex"
										style="align-content:center;text-align:center;width:10rem">
										<p class="f-left status {{ $statusLabels[$ticket->projectId][$ticket->status]['class'] ?? 'label-default' }}"
											style="width:100%;align-content:center;">
											<span class="text">
												@if(isset($statusLabels[$ticket->projectId][$ticket->status]))
													{{ $statusLabels[$ticket->projectId][$ticket->status]["name"] }}
												@else
													unknown	
												@endif
											</span>
										</p>
									</div>
								</div>
							</div>
						@endforeach
					</div>
				@endif
			</div>
		</details>
	@endforeach
</div>

<script>
    jQuery(document).ready(function() {
		var expirations = <?php echo json_encode($expirations); ?>;
		var index = 0;
		expirations.forEach(x => {
			var targetId = "expiration-" + index;
			var element = document.getElementById(targetId);
			var date = new Date(Date.parse(x));
			if (date != "Invalid Date")
				element.innerText = date.toLocaleString();
			else
				element.innerText = x;
			index++;
		})
    });
</script>

<style>
    .taskDetails {
        background-color: var(--secondary-background);
        border: 3px solid transparent;
        border-bottom: none;
        border-radius: var(--box-radius);
        border-top: none;
        box-shadow: var(--regular-shadow);
        margin-bottom: 10px;
        padding: 10px;
    }

    .taskDetails summary {
        cursor: pointer;
    }
</style>