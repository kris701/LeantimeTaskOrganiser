
<div 
	class="tw-w-full tw-h-full tw-overflow-x-hidden"
	hx-get="{{BASE_URL}}/taskOrganiser/taskOrganiserWidget/get"
	hx-trigger="htmx:afterRequest"
	class="clear"
	hx-swap="outerHTML"
>
	<p>Here is a list of the tasks you should complete today</p>
	<div id="ticketOrganiserContainer">
		@foreach ($allTickets as $ticket)
			@include('widgets::partials.todoItem', ['ticket' => $ticket, 'tpl' => $tpl, 'statusLabels' => $statusLabels, 'onTheClock' => false, 'level' => 0, 'groupKey' => 'none'])
		@endforeach
	</div>
</div>
<script>
	jQuery(document).ready(function () {
		// Remove the manual "sorting" ability from the tasks
		var container = document.getElementById("ticketOrganiserContainer")
		var items = container.querySelectorAll(".draggable-todo")
		items.forEach(x => {
			x.style.cursor = "default"
		})
		var items2 = container.querySelectorAll(".ticketBox")
		items2.forEach(x => {
			x.style.cursor = "default"
		})
	})
</script>
