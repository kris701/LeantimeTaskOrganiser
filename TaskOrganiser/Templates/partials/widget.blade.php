<div id="taskOrganiserBody"
	class="tw-w-full tw-h-full tw-overflow-x-hidden"
>
	@foreach ($settings->indexes as $setting)
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
						<p>{{$expirations[$setting->id]}}</p>
					</div>
				</ul>
			</div>
			</summary>
			<div class="tw-flex tw-flex-col" style="gap:0.5rem;margin-top:10px">
				<p>{{$setting->subtitle}}</p>
				
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
						@if ($ticket->weight >= 0)
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
						@endif
					@endforeach
				</div>
			</div>
		</details>
	@endforeach
</div>

<script>
	
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