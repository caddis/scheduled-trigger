<div style="font-size:110%;line-height:1.3;">
	<h3>Introduction</h3>
	<p>Normally when an entry becomes active (entry-date in the future) or when the <br/>
	expiration-date passes, nothing special happens. This add-on will trigger a hook when an <br />
	entry expires or becomes active. It does this by adding entries to a queue after they are editted.<br/>
	And if you change the dates of an entry, the queue will automatically be updated.</p>

	<h3>Hooks</h3>
	<p>In the settings you can set the hook to be triggered by the cron-job<br/>
	By default the <b>entry_submission_end</b> hook will be triggered.</p>
	<p>Alternativly you can set it to the custom hook <b>scheduled_trigger</b> to catch, <br/>
	when triggering the default hook would interfere with other extensions.</p>

	<h3>Cron Setup</h3>
	<p>You must setup a cronjob to call <a href="<?=$cron_endpoint ?>"><?=$cron_endpoint ?></a> occassionally. <br>
	We recommend 1-5 minute increments. A few examples of potential cron commands follow:</p>
	<ul>
		<li><code>/usr/bin/wget "<?=$cron_endpoint ?>" -O /dev/null</code></li>
		<li><code>/usr/bin/php -source "<?=$cron_endpoint ?>"</code></li>
		<li><code>curl --silent --compressed "<?=$cron_endpoint ?>"</code></li>
	</ul>
	<p>&nbsp;</p>
	<h3>Re-initialize the queue</h3>
	<p>If you have made changes to the settings (e.g. set it to queue from different channels)<br/>
	then you can <a href="<?=$reinitialize_url;?>">clean and re-initialize the queue</a>
	</p>
</div>