<?=form_open('C=addons_extensions&M=save_extension_settings');?>
	<div>
		<input type="hidden" name="file" value="scheduled_trigger" />
		<input type="hidden" name="XID" value="<?=XID_SECURE_HASH;?>" />
	</div>
	<table cellpadding="0" cellspacing="0" style="width:100%" class="mainTable">
		<colgroup>
			<col style="width:20%" />
			<col style="width:80%" />
		</colgroup>
		<thead>
			<tr>
				<th scope="col"><?=lang('preference')?></th>
				<th scope="col"><?=lang('setting')?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<h3>Channels</h3>
				</td>
				<td>
					<?php 
					foreach ($channel_checkboxes as $channel_checkbox)
					{
						echo $channel_checkbox . "<br />\n";
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<h3>Hooks to trigger</h3>
				</td>
				<td>
					<?php 
					foreach ($triggers as $trigger_checkbox)
					{
						echo $trigger_checkbox . "<br />\n";
					}
					?>
				</td>
			</tr>
		</tbody>
	</table>
	<input type="submit" class="submit" value="<?=lang('submit')?>" />
</form>