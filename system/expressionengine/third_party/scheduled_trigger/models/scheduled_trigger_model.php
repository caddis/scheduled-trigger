<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scheduled Trigger Model
 *
 * @package Scheduled Trigger
 * @author  Caddis
 * @link    http://www.caddis.co
 */

class Scheduled_trigger_model extends CI_Model {

	private $_table = 'scheduled_trigger';
	private $_queue_table = 'scheduled_trigger_queue';
	private $_log_table = 'scheduled_trigger_log';
	private $_entry_table = 'channel_titles';
	private $_data_table = 'channel_data';

	private $settings = array();
	private $now;

	public function check($site_id)
	{
		$this->now = ee()->localize->now;

		// Query for previous check

		$results = ee()->db->select('check_date')
			->from($this->_table)
			->where('site_id', $site_id)
			->get();

		// Initialize pre-existing queue on first cron hit

		if ($results->num_rows() == 0) {
			$this->_init_queue($site_id);

			return false;
		} else {
			$check_date = $results->row('check_date');

			// Update check timestamp

			ee()->db->update($this->_table, array(
				'site_id' => $site_id,
				'check_date' => $this->now
			));

			// Check for scheduled posts

			$results = ee()->db->select('Q.entry_id')
				->from($this->_queue_table . ' Q')
				->join('channel_titles T', 'T.entry_id = Q.entry_id')
				->where('Q.site_id', $site_id)
				->where('T.entry_date <=', $this->now)
				->where('T.entry_date >=', $check_date)
				->where('type', 1)
				->get();

			foreach ($results->result_array() as $row) {
				$entry_id = $row['entry_id'];

				$this->_trigger_entry($site_id, $entry_id, 1);
				$this->_log_entry($site_id, $entry_id, 1);
			}

			// Check for expired posts

			$results = ee()->db->select('Q.entry_id')
				->from($this->_queue_table . ' Q')
				->join('channel_titles T', 'T.entry_id = Q.entry_id')
				->where('Q.site_id', $site_id)
				->where('T.expiration_date <=', $this->now)
				->where('T.expiration_date >=', $check_date)
				->where('type', 2)
				->get();

			foreach ($results->result_array() as $row) {
				$entry_id = $row['entry_id'];

				$this->_trigger_entry($site_id, $entry_id, 2);
				$this->_log_entry($site_id, $entry_id, 2);
			}
		}

		return true;
	}

	public function add_queue($site_id, $entry_id, $type)
	{
		// Make sure the check date has been set

		$results = ee()->db->select('check_date')
			->from($this->_table)
			->where('site_id', $site_id)
			->get();

		if ($results->num_rows() != 0) {
			$results = ee()->db->select('id')
				->from($this->_queue_table)
				->where('site_id', $site_id)
				->where('entry_id', $entry_id)
				->where('type', $type)
				->get();

			// Insert if the entry doesn't pre-exist in the queue

			if ($results->num_rows() == 0) {
				ee()->db->insert($this->_queue_table, array(
					'site_id' => $site_id,
					'entry_id' => $entry_id,
					'type' => $type
				));
			}
		}
	}

	public function remove_queue($site_id, $entry_id, $type)
	{
		ee()->db->delete($this->_queue_table, array(
			'site_id' => $site_id,
			'entry_id' => $entry_id,
			'type' => $type
		));
	}

	public function get_queue($site_id)
	{
		$queue = array();

		$results = ee()->db->select('Q.id, Q.entry_id, CASE Q.type WHEN 1 THEN "Publishing" ELSE "Expiring" END AS type,
				T.title, CASE Q.type WHEN 1 THEN T.entry_date ELSE T.expiration_date END AS date', false)
			->from($this->_queue_table . ' Q')
			->join('channel_titles T', 'T.entry_id = Q.entry_id')
			->where('Q.site_id', $site_id)
			->order_by('T.entry_date, T.expiration_date', 'asc')
			->get();

		foreach ($results->result_array() as $row) {
			$entry = array(
				'id' => $row['id'],
				'entry_id' => $row['entry_id'],
				'type' => $row['type'],
				'title' => $row['title'],
				'date' => ee()->localize->human_time($row['date'])
			);

			array_push($queue, $entry);
		}

		return $queue;
	}

	public function get_log($site_id)
	{
		$log = array();

		$results = ee()->db->select('L.id, L.entry_id, CASE L.type WHEN 1 THEN "Published" ELSE "Expired" END AS type,
				L.triggered, T.title', false)
			->from($this->_log_table . ' L')
			->join('channel_titles T', 'T.entry_id = L.entry_id')
			->where('L.site_id', $site_id)
			->order_by('L.triggered', 'desc')
			->limit(20)
			->get();

		if ($results->num_rows() > 0) {
			foreach ($results->result_array() as $row) {
				$entry = array(
					'id' => $row['id'],
					'entry_id' => $row['entry_id'],
					'type' =>  $row['type'],
					'date' => ee()->localize->human_time($row['triggered']),
					'title' => $row['title']
				);

				array_push($log, $entry);
			}
		}

		return $log;
	}

	public function remove_log($site_id, $entry_id)
	{
		ee()->db->delete($this->_log_table, array(
			'site_id' => $site_id,
			'entry_id' => $entry_id
		));
	}

	public function reset_queue($site_id)
	{
		$this->db->delete($this->_queue_table, array('site_id' => $site_id));
		$this->db->delete($this->_table, array('site_id' => $site_id)); 

		return $this->check($site_id);
	}

	public function get_entry_data($entry_id)
	{
		$results = ee()->db->select('channel_id, expiration_date')
			->from($this->_entry_table)
			->where('entry_id', $entry_id)
			->get();

		return $results->row();
	}

	private function _get_extension_settings()
	{
		$query = $this->db->get_where('extensions', array(
			'class' => 'Scheduled_trigger_ext'
		), 1);
		
		if ($query->num_rows() == 0) {
			show_error('<h2>Cannot find extension settings</h2><p>Was the extension not installed?</p>');
		}

		$row = $query->row();
		$this->settings = unserialize($row->settings);

		if (! isset($this->settings['triggers'])) {
			$this->settings['triggers'] = array('entry_submission_end');
		}
	}

	private function _init_queue($site_id)
	{
		ee()->load->model('channel_model');

		// Fetch extension settings
		
		$this->_get_extension_settings();

		// Fetch channels and restrict queueing to selected channels from settings		

		$channel_data = ee()->channel_model->get_channels()->result();

		$channels = array(0);

		foreach ($channel_data as $item) {
			if (@$this->settings['channels'][$item->channel_id]) {
				$channels[] = (int) $item->channel_id;
			}
		}

		// Setup check table

		ee()->db->insert($this->_table, array(
			'site_id' => $site_id,
			'check_date' => $this->now
		));

		// Insert scheduled posts

		$results = ee()->db->select('entry_id')
			->from($this->_entry_table)
			->where('site_id', $site_id)
			->where('entry_date >', $this->now)
			->where_in('channel_id', $channels)
			->get();

		foreach ($results->result_array() as $row) {
			$this->add_queue($site_id, $row['entry_id'], 1);
		}

		// Insert expiring posts
		
		$results = ee()->db->select('entry_id')
			->from($this->_entry_table)
			->where('site_id', $site_id)
			->where('expiration_date >', $this->now)
			->where_in('channel_id', $channels)
			->get();

		foreach ($results->result_array() as $row) {
			$this->add_queue($site_id, $row['entry_id'], 2);
		}
	}

	private function _trigger_entry($site_id, $entry_id, $type)
	{
		ee()->load->library('api');
		ee()->api->instantiate('channel_entries');

		// Get entry meta data

		$meta = ee()->db->select('*')
			->from($this->_entry_table)
			->where('site_id', $site_id)
			->where('entry_id', $entry_id)
			->get();

		// Get entry data

		$data = ee()->db->select('*')
			->from($this->_data_table)
			->where('site_id', $site_id)
			->where('entry_id', $entry_id)
			->get();

		// Remove from queue

		$this->remove_queue($site_id, $entry_id, $type);

		// Trigger submission hook

		if ($meta->num_rows() > 0) {
			$data = $data->result_array();
			$data = $data[0];

			// Remove entry_id from data to simulate new entry

			$data['entry_id'] = '';

			$meta = $meta->result_array();
			$meta = $meta[0];

			// Fetch triggers from settings

			$this->_get_extension_settings();

			foreach ($this->settings['triggers'] as $trigger) {
				ee()->extensions->call($trigger, $entry_id, $meta, $data);
			}
		}
	}

	private function _log_entry($site_id, $entry_id, $type)
	{
		ee()->db->insert($this->_log_table, array(
			'site_id' => $site_id,
			'entry_id' => $entry_id,
			'type' => $type,
			'triggered' => $this->now
		));
	}
}