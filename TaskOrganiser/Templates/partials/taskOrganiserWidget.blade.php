@props([
    'includeTitle' => true,
    'onTheClock' => false,
])

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
							&nbsp;<a href="javascript:void(0);" class="edit-button"
									data-tippy-content="{{ __('text.edit_task_headline') }}"><i class="fa fa-edit"></i></a>

						</div>
						<div class="tw-hidden edit-form">
							<form class="tw-flex tw-flex-row tw-items-center tw-gap-2"
								hx-post="{{ BASE_URL }}/hx/widgets/myToDos/updateTitle"
								hx-target=".ticket-headline-{{ $ticket['id'] }}"
								onsubmit="jQuery(this).closest('.edit-form').find('.cancel-edit-task').click();htmx.trigger('#taskOrganiserBody', 'onReloadView')"
							>
								<input type="hidden" name="id" value="{{ $ticket['id'] }}"/>
								<div>
									<input type="text" class="main-title-input"
										style="font-size:var(--base-font-size); margin-bottom:0px"
										value="{{ $ticket['headline'] }}" name="headline"/>
								</div>
								<div>
									<button type="submit" name="edit" class="btn btn-primary">
										<i class="fa fa-check"></i>
									</button>
								</div>
								<div>
									<a href="javascript:void(0);" class="btn cancel-edit-task" data-group="none"><i
											class="fa fa-x"></i></a>
								</div>
							</form>
						</div>
					</div>

					@dispatchEvent('beforeStatusUpdate')
					<div
						class="status-container tw-flex-1 tw-justify-items-end tw-flex tw-flex-row tw-justify-end tw-gap-2 tw-content-center">
						<div class="tw-content-center tw-mr-[10px] dropdown ticketDropdown statusDropdown colorized show">
							<a class="dropdown-toggle f-left status {{ $statusLabels[$ticket['projectId']][$ticket['status']]["class"] ?? 'label-default' }}"
							href="javascript:void(0);"
							role="button"
							id="statusDropdownMenuLink{{ $ticket['id'] }}"
							data-toggle="dropdown"
							aria-haspopup="true"
							aria-expanded="false">
								<span class="text">
									@if(isset($statusLabels[$ticket['projectId']][$ticket['status']]))
										{{ $statusLabels[$ticket['projectId']][$ticket['status']]["name"] }}
									@else
										unknown
									@endif
								</span>
								&nbsp;<i class="fa fa-caret-down" aria-hidden="true"></i>
							</a>
							<ul class="dropdown-menu pull-right"
								aria-labelledby="statusDropdownMenuLink{{ $ticket['id'] }}">
								<li class="nav-header border">{{ __("dropdown.choose_status") }}</li>
								@foreach ($statusLabels[$ticket['projectId']] as $key => $label)
									<li class='dropdown-item'>
										<a href="javascript:void(0);"
										class='{{ $label["class"] }}'
										data-label='{{ $label["name"] }}'
										data-value='{{ $ticket['id'] }}_{{ $key }}_{{ $label["class"] }}'
										id='ticketStatusChange{{$ticket['id'] . $key }}'
										hx-post="{{ BASE_URL }}/widgets/myToDos/updateStatus"
										hx-swap="none"
										hx-vals='{"id": "{{ $ticket['id'] }}", "status": "{{ $key }}"}'>
											{{ $label["name"] }}
										</a>
									</li>
								@endforeach
							</ul>
						</div>
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

	jQuery('.ticket-title').each(function(){
		let currentTitle = jQuery(this);
		jQuery(this).hover(function () {
			jQuery(this).find(".edit-button").show();
		},
			function(){
				jQuery(this).find(".edit-button").hide();

		});

		jQuery(this).find(".edit-button").click(function() {
			currentTitle.find(".edit-button").hide();
			currentTitle.find('.title-text').hide();
			currentTitle.find('.edit-form').show();
		});

		jQuery(this).find(".edit-form .cancel-edit-task").click(function() {
			currentTitle.find('.title-text').show();
			currentTitle.find('.edit-form').hide();
		});
	});

	jQuery('.dropdown-item').each(function() {
		let current = jQuery(this);
		current.click(function() {
			htmx.trigger('#taskOrganiserBody', 'onReloadView');
		})
	});
</script>
