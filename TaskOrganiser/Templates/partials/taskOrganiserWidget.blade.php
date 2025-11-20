<div id="taskOrganiserBody"
	class="tw-w-full tw-h-full tw-overflow-x-hidden"
>
	<p>Here is a list of the tasks you should complete today</p>
	<div class="ticketBox" style="cursor:default">
		<div class="tw-flex tw-flex-row" style="gap:1rem">
			<p>ID</p>
			<p class="tw-flex-1">Name and Project</p>
			<p style="align-content:center;text-align:center;width:10rem">Priority</p>
			<p style="align-content:center;text-align:center;width:10rem">Effort</p>
			<p style="align-content:center;text-align:center;width:10rem">Status</p>
		</div>
	</div>
	<div id="ticketOrganiserContainer">
		@foreach ($allTickets as $ticket)
			<div class="ticketBox" style="cursor:default">
				<div class="tw-flex tw-flex-row" style="gap:1rem">
					<div style="align-content:center">
						<span>#{{ $ticket['id'] }}</span>
					</div>

					<div class="tw-flex-1 ticket-title ticket-title-wrapper">
						<div class="title-text">
							<small style="display:inline-block; ">{{ $ticket['projectName'] }}</small> <br/>
							<strong><a href="#/tickets/showTicket/{{ $ticket['id'] }}" preload="mouseover"
									class="ticket-headline-{{ $ticket['id'] }}">{{ $ticket['headline'] }}</a></strong>
						</div>
					</div>
					
					<div
						style="align-content:center;text-align:center;width:10rem"
						<p class="f-left">
							<span class="text">
								@if(isset($priorityLabels[$ticket['priority']]))
									{{ $priorityLabels[$ticket['priority']] }}
								@else
									No priority set
								@endif
							</span>
						</p>
					</div>

					<div
						style="align-content:center;text-align:center;width:10rem"
						<p class="f-left">
							<span class="text">
								@if(isset($effortLabels[$ticket['storypoints']]))
									{{ $effortLabels[$ticket['storypoints']] }}
								@else
									No effort set
								@endif
							</span>
						</p>
					</div>

					<div
						style="align-content:center;text-align:center;width:10rem"
						<p class="f-left status {{ $statusLabels[$ticket['projectId']][$ticket['status']]['class'] ?? 'label-default' }}">
							<span class="text">
								@if(isset($statusLabels[$ticket['projectId']][$ticket['status']]))
									{{ $statusLabels[$ticket['projectId']][$ticket['status']]["name"] }}
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
</div>

<script>
	
</script>
