<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scheduled Trigger Module Control Panel
 *
 * @package Scheduled Trigger
 * @author  Caddis
 * @link    http://www.caddis.co
 */

class Scheduled_trigger_mcp {

	public $page_limit = 20;

	private $_site_id;
	private $_base_url;
	private $_root_url;
	private $_method_url;
	private $_post_url;

	public function __construct()
	{
		$this->_site_id = ee()->config->item('site_id');

		$this->_base_url = 'C=addons_modules' . AMP . 'M=show_module_cp' . AMP . 'module=scheduled_trigger';
		$this->_root_url = BASE . AMP . $this->_base_url;
		$this->_method_url = $this->_root_url . AMP . 'method=';
		$this->_post_url = $this->_base_url . AMP . 'method=';

		ee()->cp->set_right_nav(array(
			'Instructions' => $this->_root_url,
			'Queue' => $this->_method_url . 'queue',
			'Log' => $this->_method_url . 'log',
			'Settings' => BASE . AMP . 'C=addons_extensions&amp;M=extension_settings&amp;file=scheduled_trigger'
		));
	}

	public function index()
	{
		ee()->view->cp_page_title = 'Instructions';

		$site_url = ee()->config->item('site_url');

		// Action ID

		$action_id = ee()->cp->fetch_action_id('Scheduled_trigger', 'execute');

		// View data

		$cron_endpoint = $site_url;

		$this->view_data = array(
			'cron_endpoint' => $cron_endpoint . '/index.php?ACT=' . $action_id
		);

		return ee()->load->view('scheduled_trigger_index', $this->view_data, true);
	}

	public function queue()
	{
		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');

		ee()->view->cp_page_title = 'Queue';

		// Queue clearing message

		if ($msg = ee()->session->flashdata('msg')) {
			ee()->javascript->output(array(
				'$.ee_notice("' . lang($msg) . '",{type:"success", open:true});',
				'window.setTimeout(function() {$.ee_notice.destroy()}, 2000);'
			));
		}

		// View data

		$this->view_data = array(
			'session_id' => '',
			'queue' => ee()->scheduled_trigger->get_queue($this->_site_id),
			'reinitialize_url' => $this->_method_url . 'reset'
		);

		return ee()->load->view('scheduled_trigger_queue', $this->view_data, true);
	}

	public function reset()
	{
		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');

		ee()->scheduled_trigger->reset_queue($this->_site_id);

		ee()->session->set_flashdata('msg', 'reset');

		ee()->functions->redirect($this->_method_url . 'queue');
	}

	public function log()
	{
		ee()->load->model('scheduled_trigger_model', 'scheduled_trigger');

		ee()->view->cp_page_title = 'Log';

		// View data

		$this->view_data = array(
			'session_id' => '',
			'log' => ee()->scheduled_trigger->get_log($this->_site_id)
		);

		return ee()->load->view('scheduled_trigger_log', $this->view_data, true);
	}
}