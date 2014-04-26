<?=form_open($post_url, array('id' => 'scheduled_trigger_settings')) ?>

<table cellpadding="0" cellspacing="0" class="mainTable">
	<thead>
		<tr>
			<th width="20%"><?=lang('preference'); ?></th>
			<th width="80%"><?=lang('setting'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>
				<label>Channels</label><br>
				Only selected channel entries are queued
			</td>
			<td>
				<?php 
				foreach ($channel_checkboxes as $channel_checkbox) {
					echo $channel_checkbox . "<br />\n";
				}
				?>
			</td>
		</tr>
		<tr>
			<td>
				<label>Triggered Hook</label><br>
				Hooks called by entry updates
			</td>
			<td>
				<?php 
				foreach ($triggers as $trigger_checkbox) {
					echo $trigger_checkbox . "<br />\n";
				}
				?>
			</td>
		</tr>
	</tbody>
</table>
<input type="submit" class="submit" value="<?=lang('submit'); ?>">

<?=form_close() ?>