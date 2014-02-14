<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scheduled Trigger Extension
 *
 * @package Scheduled Trigger
 * @author  Caddis
 * @link    http://www.caddis.co
 */

include(PATH_THIRD . 'scheduled_trigger/config.php');

class Scheduled_trigger_ext {

	public $name = SCHEDULED_TRIGGER_NAME;
	public $version = SCHEDULED_TRIGGER_VER;
	public $description = SCHEDULED_TRIGGER_DESC;
	public $docs_url = '';
	public $settings_exist	= 'n';
	public $settings = array();

	/**
	 * Constructor
	 *
	 * @param  mixed Settings array or empty string if none exist
	 * @return void
	 */
	public function __construct($settings = array())
	{
		$this->settings = $settings;

		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');

		$this->_site_id = ee()->config->item('site_id');
	}

	/**
	 * Activate Extension
	 * 
	 * @return void
	 */
	public function activate_extension()
	{
		$hooks = array(
			'entry_submission_end',
			'delete_entries_loop',
			'update_multi_entries_loop'
		);

		foreach ($hooks as $hook)
		{
			$this->_add_hook($hook);
		}
	}

	/**
	 * Update Extension
	 *
	 * @return mixed void on update / false if none
	 */
	public function update_extension($current = '')
	{
		if ($current == $this->version)
		{
			return false;
		}

		return true;
	}

	/**
	 * Disable Extension
	 *
	 * @return void
	 */
	public function disable_extension()
	{
		ee()->db->where('class', __CLASS__);
		ee()->db->delete('extensions');
	}

	public function entry_submission_end($entry_id, $meta, $data)
	{
		$now = ee()->localize->now;

		$entry_date = $meta['entry_date'];
		$expiration_date = $meta['expiration_date'];

		if ($entry_date > $now)
		{
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 1);
		}
		else
		{
			ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 1);
		}

		if ($expiration_date > $now)
		{
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 2);
		}
		else
		{
			ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 2);
		}

		return $data;
	}

	public function update_multi_entries_loop($entry_id, $data)
	{
		$now = ee()->localize->now;

		$entry_date = $data['entry_date'];
		$expiration_date = $data['expiration_date'];

		if ($entry_date > $now)
		{
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 1);
		}

		if ($expiration_date > $now)
		{
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 2);
		}
	}

	public function delete_entries_loop($entry_id, $channel_id)
	{
		ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 1);
		ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 2);

		ee()->scheduled_trigger->remove_log($this->_site_id, $entry_id);
	}

	private function _add_hook($name)
	{
		ee()->db->insert('extensions', array(
			'class' => __CLASS__,
			'method' => $name,
			'hook' => $name,
			'settings' => '',
			'priority' => 10,
			'version' => $this->version,
			'enabled' => 'y'
		));
	}
}