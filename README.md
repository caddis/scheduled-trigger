# Scheduled Trigger 1.0.1b

Trigger hooks when scheduled ExpressionEngine entries go live or expire.

Entry dates in ExpressionEngine are generally only used as filters when listing and viewing entries. This add-on adds entries that are about to expire or are about to go live to a queue, and updates the queue after an entry has been editted.

## Example

For example, a channel where members can publish classifieds which have a default expiration-date of 30 days after publish. When the entry expires the queue will trigger a system hook. This allows another add-on to listen to the hook, and for instance send an email to the author that the entry has expired.

## Setup

Move the scheduled_trigger directory to your ExpressionEngine third_party folder and install from the control panel. Follow the instructions on the module page to setup the required cronjob.

## The hook

The extension hook is called with the $data['entry_id'] cleared, as if a new entry is published.  
```ee()->extensions->call('hook_name', $entry_id, $meta, $data);```  
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