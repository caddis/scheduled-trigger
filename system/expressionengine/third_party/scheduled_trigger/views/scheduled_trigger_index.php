<p>Normally when an entry becomes active (entry date in the future) or when the expiration date passes, ExpressionEngine is unaware. This add-on will trigger a hook when an entry expires or becomes active. It does this by adding entries to a queue after they are updated and if you change the dates of an entry, the queue will automatically be updated.</p>
<h3>Hooks</h3>
<p>In the settings you can set the hook to be triggered by the cronjob. By default the <b>entry_submission_end</b> hook will be triggered. Alternatively, you can set the custom hook <b>scheduled_trigger</b> to be hit when triggering the default hook would interfere with other extensions.</p>
<h3>Cron</h3>
<p>You must setup a cronjob to call <a href="<?=$cron_endpoint ?>"><?=$cron_endpoint ?></a>. We recommend 1-5 minute increments. A few examples of potential cron commands follow:</p>
<ul>
	<li><code>/usr/bin/wget "<?=$cron_endpoint ?>" -O /dev/null</code></li>
	<li><code>/usr/bin/php -source "<?=$cron_endpoint ?>"</code></li>
	<li><code>curl --silent --compressed "<?=$cron_endpoint ?>"</code></li>
</ul>