<p>This extension will trigger the <b>entry_submission_end</b> hook when scheduled entries publish or expire. This ensures that modules relying on the hook can process the data accordingly.</p>
<h3>Cron Setup</h3>
<p>You must setup a cronjob to call <a href="<?=$cron_endpoint ?>"><?=$cron_endpoint ?></a> occassionally. We recommend 1-5 minute increments. A few examples of potential cron commands follow:</p>
<ul>
	<li><code>/usr/bin/wget "<?=$cron_endpoint ?>" -O /dev/null</code></li>
	<li><code>/usr/bin/php -source "<?=$cron_endpoint ?>"</code></li>
	<li><code>curl --silent --compressed "<?=$cron_endpoint ?>"</code></li>
</ul>