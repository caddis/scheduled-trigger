<table cellpadding="0" cellspacing="0" class="mainTable">
	<thead>
		<tr>
			<th width="20%">#</th>
			<th width="20%">Title</th>
			<th width="20%">Type</th>
			<th width="20%">Date</th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($queue) === 0) { ?>
		<tr>
			<td colspan="4">No queued entries available.</td>
		</tr>
		<?php } else { ?>
		<?php foreach ($queue as $row) { ?>
		<tr>
			<td><?=$row['entry_id'] ?></td>
			<td><a href="<?=BASE ?>&D=cp&C=content_publish&M=entry_form&entry_id=<?=$row['entry_id'] ?>"><?=$row['title'] ?></a></td>
			<td><?=$row['type'] ?></td>
			<td><?=$row['date'] ?></td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>