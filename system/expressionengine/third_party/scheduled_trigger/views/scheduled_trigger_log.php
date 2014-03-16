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
		<?php if (count($log) === 0) { ?>
		<tr>
			<td colspan="5">No logged triggers available.</td>
		</tr>
		<?php } else { ?>
		<?php foreach ($log as $row) { ?>
		<tr>
			<td><?=$row['entry_id'] ?></td>
			<td><a href="<?=BASE;?>&amp;C=content_publish&amp;M=entry_form&amp;entry_id=<?=$row['entry_id'];?>"><?=$row['title'] ?></a></td>
			<td><?=$row['type'] ?></td>
			<td><?=$row['date'] ?></td>
		</tr>
		<?php } ?>
		<?php } ?>
	</tbody>
</table>