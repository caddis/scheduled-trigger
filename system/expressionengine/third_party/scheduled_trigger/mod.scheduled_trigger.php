<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scheduled Trigger Module Frontend File
 *
 * @package Scheduled Trigger
 * @author  Caddis
 * @link    http://www.caddis.co
 */

class Scheduled_trigger {

	private $_site_id;

	public function __construct()
	{
		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');

		$this->_site_id = ee()->config->item('site_id');
	}

	public function execute()
	{
		ee()->scheduled_trigger->check($this->_site_id);
	}
}