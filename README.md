# Scheduled Trigger 1.1.3

Trigger entry submission hooks when scheduled ExpressionEngine entries go live or expire.

## Overview

Problem: ExpressionEngine currently has no system in place to trigger actions or hooks when an entry goes live or expires.

Solution: Scheduled Trigger. This add-on provides an endpoint that can be executed with a scheduled cron job configured to run at your desired interval. It's extremely lightweight so setting it for every 5 minutes or so is no problem.

## Details

Until now, if you relied on entry submission hooks to trigger cache clearing (be it native template caching, or a third party add-on for static caching or similar), scheduling entries to go live or expire at a specified time was pretty useless.

Scheduled Trigger adds any entries that are scheduled to go live in the future, or set to expire in the future to a queue. Every time the end point is executed (via scheduled cron) it checks the items in the queue against the current time and date. If an entry is past the schedule date, triggers are executed.

## Examples

Let's say you have a channel where members can publish classified adds which have a default expiration of 30 days after publishing. When the entry  expires, the queue will trigger the appropriate EE entry submission hooks. This would allow you to have another add-on based on the entry submission hooks listening for expiration that would, for instance, send an email to the author that the entry has expired.

Or perhaps you are using EE template caching or using an add-on like [Stash] with [Mustash] to clear static caching when entry submission hooks are triggered. EE will not natively trigger hooks which would cause the cache clearing rules to be hit when the scheduled time arrives. Scheduled Trigger will trigger those hooks and the cache breaking rules will be executed.

[Stash]:https://github.com/croxton/Stash
[Mustash]:http://devot-ee.com/add-ons/mustash

## Setup

Download and extract the package, then move the scheduled_trigger directory to your ExpressionEngine third_party folder. From the Add-Ons > Modules area of the EE control panel, install the module.

Once installed, you can find information about setting up the cron, and the URL for the endpoint to use with the cron setup on the Scheduled Trigger Module Page in the EE Control Panel.

## Module Control Panel

The module control panel in ExpressionEngine has four tab areas:

### Instructions

This is where you can get the instructions and end point for the cron job.

![Instructions](http://files.caddis.co/addons/scheduled-trigger/instructions.jpg)

![cPanel](http://files.caddis.co/addons/scheduled-trigger/cpanel.jpg)

### Queue

Here you can see any entries that have been schedule to publish or expire in the future.

![Queue](http://files.caddis.co/addons/scheduled-trigger/queue.jpg)

### Log

The log shows you recent entries that were formerly in the queue and have triggered hooks.

![Log](http://files.caddis.co/addons/scheduled-trigger/log.jpg)

### Settings

Here you can choose which channels to include in the queue, and which hooks can be triggered.

![cPanel](http://files.caddis.co/addons/scheduled-trigger/settings.jpg)

## Hook Information

The extension hook is called with the $data['entry_id'] cleared, as if a new entry is published.

	ee()->extensions->call('hook_name', $entry_id, $meta, $data);

Note: Scheduled Trigger does not handle return data, it is just a queued trigger.

## License

Copyright 2014 Caddis Interactive, LLC

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

	http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.