
<div class="tw-w-full tw-h-full tw-overflow-x-hidden">
	<p>Ticket list:</p>
	@foreach ($allTickets as $ticket)
		<p>{{$ticket['id']}}: '{{$ticket['headline']}}'</p>
	@endforeach
</div>
<script>
  // script
</script>
