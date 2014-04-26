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
	public $settings_exist	= 'y';
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

		$this->_site_id = ee()->config->item('site_id');
	}

	public function settings_form($current)
	{
		ee()->load->model('channel_model');

		$base_url = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=scheduled_trigger';
		$root_url = BASE . AMP . $base_url;
		$method_url = $root_url . AMP . 'method=';
		$post_url = $base_url . AMP . 'method=';

		ee()->cp->set_right_nav(array(
			'Instructions' => $root_url,
			'Queue' => $method_url . 'queue',
			'Log' => $method_url . 'log',
			'Settings' => $method_url . 'extension_settings'
		));

		if (! isset($current['channels'])) {
			$current['channels'] = array();
		}

		if (! isset($current['triggers'])) {
			$current['triggers'] = array();
		}
		
		$this->settings = $current;

		$vars = array( 
			'channel_checkboxes' => array(),
			'triggers' => array(),
			'post_url' => 'C=addons_extensions&M=save_extension_settings&file=scheduled_trigger'
		);

		$channel_data = ee()->channel_model->get_channels()->result();

		foreach ($channel_data as $i => $item) {
			$checked = (bool) @$this->settings['channels'][$item->channel_id];

			$vars['channel_checkboxes'][$i] = form_checkbox(array(
				'name' => "st_channel[{$item->channel_id}]",
				'id' => "st_channel_{$item->channel_id}",
				'value' => $item->channel_id,
				'checked' => $checked,
			));

			$vars['channel_checkboxes'][$i] .= form_label(" {$item->channel_title}", 'st_channel_{$item->channel_id}');
		}

		$vars['triggers'][] = form_checkbox(array(
			'name' => 'st_triggers[entry_submission_end]',
			'id' => 'st_triggers_entry_submission_end',
			'value' => '1',
			'checked' => in_array('entry_submission_end', $this->settings['triggers']),
		)) . form_label(' entry_submission_end', 'st_triggers_entry_submission_end');

		$vars['triggers'][] = form_checkbox(array(
			'name' => 'st_triggers[scheduled_trigger]',
			'id' => 'st_triggers_scheduled_trigger',
			'value' => '1',
			'checked' => in_array('scheduled_trigger', $this->settings['triggers']),
		)) . form_label(' scheduled_trigger', 'st_triggers_scheduled_trigger');

		if ($msg = ee()->session->flashdata('msg')) {
			ee()->javascript->output(array(
				'$.ee_notice("' . lang($msg) . '", {type:"success", open:true});',
				'window.setTimeout(function() {$.ee_notice.destroy()}, 2000);'
			));
		}

		return ee()->load->view('scheduled_trigger_settings', $vars, TRUE);
	}

	public function save_settings()
	{
		// Fetch channels

		$posted_channels = ee()->input->post('st_channel');
		
		ee()->load->model('channel_model');
		$channel_data = ee()->channel_model->get_channels()->result();

		foreach ($channel_data as $item) {
			$this->settings['channels'][$item->channel_id] = (isset($posted_channels[$item->channel_id]));
		}

		// Fetch triggers

		$posted_triggers = ee()->input->post('st_triggers');
		$this->settings['triggers'] = array();		 

		if (isset($posted_triggers['entry_submission_end'])) $this->settings['triggers'][] = 'entry_submission_end';
		if (isset($posted_triggers['scheduled_trigger'])) $this->settings['triggers'][] = 'scheduled_trigger';


		ee()->db->update(
			'extensions', 
			array('settings' => serialize($this->settings)), 
			array('class'=>'Scheduled_trigger_ext')
		);

		ee()->session->set_flashdata('msg', 'Settings saved');
		ee()->functions->redirect(BASE.AMP.'C=addons_extensions&amp;M=extension_settings&amp;file=scheduled_trigger');
	}

	public function _check_channel($channel_id)
	{
		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');
		return (bool) @$this->settings['channels'][$channel_id];
	}


	/**
	 * Activate Extension
	 * 
	 * @return void
	 */
	public function activate_extension()
	{
		ee()->load->model('channel_model');

		$hooks = array(
			'entry_submission_end' => 'entry_submission_end',
			'delete_entries_loop' => 'delete_entries_loop',
			'update_multi_entries_loop' => 'update_multi_entries_loop',
			'scheduled_trigger' => 'entry_submission_end'
		);

		// Default settings

		$this->settings = array(
			'channels' => array(),
			'triggers' => array('entry_submission_end')
		);

		$channel_data = ee()->channel_model->get_channels()->result();

		foreach ($channel_data as $item) {
			$this->settings['channels'][$item->channel_id] = TRUE;
		}

		// Add the hooks

		foreach ($hooks as $hook => $method) {
			$this->_add_hook($hook, $method);
		}
	}

	/**
	 * Update Extension
	 *
	 * @return mixed void on update / false if none
	 */
	public function update_extension($current = '')
	{
		if ($current == $this->version) {
			return false;
		}

		if ($current < '1.1') {
			$this->_add_hook('scheduled_trigger', 'entry_submission_end');
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

		$allow_channel = $this->_check_channel($meta['channel_id']);

		if ($entry_date > $now and $allow_channel) {
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 1);
		} else {
			ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 1);
		}

		if ($expiration_date > $now and $allow_channel) {
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 2);
		} else {
			ee()->scheduled_trigger->remove_queue($this->_site_id, $entry_id, 2);
		}

		return $data;
	}

	public function update_multi_entries_loop($entry_id, $data)
	{
		$query = ee()->db
			->select('channel_id')
			->where('entry_id', $entry_id)
			->get('channel_titles');

		$row = $query->row();

		$now = ee()->localize->now;

		$entry_date = $data['entry_date'];
		$expiration_date = $data['expiration_date'];

		$allow_channel = $this->_check_channel($row->channel_id);

		if ($entry_date > $now and $allow_channel) {
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 1);
		}

		if ($expiration_date > $now and $allow_channel) {
			ee()->scheduled_trigger->add_queue($this->_site_id, $entry_id, 2);
		}
	}

	public function delete_entries_loop($entry_id, $channel_id)
	{
		$allow_channel = $this->_check_channel($meta['channel_id']);

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
			'settings' => serialize($this->settings),
			'priority' => 10,
			'version' => $this->version,
			'enabled' => 'y'
		));
	}
}