<div id="taskOrganiserBody"
	class="tw-w-full tw-h-full tw-overflow-x-hidden"
	hx-get="{{BASE_URL}}/taskOrganiser/taskOrganiserWidget/get"
	hx-trigger="onReloadView from:body"
>
	<p>Here is a list of the tasks you should complete today</p>
	<div id="ticketOrganiserContainer">
		@foreach ($allTickets as $ticket)
			<div class="ticketBox">
				<div class="tw-flex tw-flex-row">
					<div class="tw-flex-1 ticket-title ticket-title-wrapper">
						<div class="title-text">
							<small style="display:inline-block; ">{{ $ticket['projectName'] }}</small> <br/>
							<strong><a href="#/tickets/showTicket/{{ $ticket['id'] }}" preload="mouseover"
									class="ticket-headline-{{ $ticket['id'] }}">{{ $ticket['headline'] }}</a></strong>
						</div>
					</div>

					@dispatchEvent('beforeStatusUpdate')
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
	@dispatchEvent('scripts.afterOpen')

	jQuery(document).ready(function () {
		// Remove the manual "sorting" ability from the tasks
		var container = document.getElementById("ticketOrganiserContainer")
		var items = container.querySelectorAll(".ticketBox")
		items.forEach(x => {
			x.style.cursor = "default"
		})
	})
</script>
