<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Scheduled Trigger Module Install/Update File
 *
 * @package Scheduled Trigger
 * @author  Caddis
 * @link    http://www.caddis.co
 */

include(PATH_THIRD . 'scheduled_trigger/config.php');

class Scheduled_trigger_upd {

	public $name = SCHEDULED_TRIGGER_NAME;
	public $version = SCHEDULED_TRIGGER_VER;

	/**
	 * Install
	 *
	 * @return boolean
	 */
	public function install()
	{
		ee()->load->dbforge();

		// Module record

		ee()->db->insert('modules', array(
			'module_name' => 'Scheduled_trigger',
			'module_version' => $this->version,
			'has_cp_backend' => 'y',
			'has_publish_fields' => 'n'
		));

		// Trigger endpoint

		ee()->db->insert('actions', array(
			'class' => 'Scheduled_trigger',
			'method' => 'execute'
		));

		// Scheduled Trigger table

		ee()->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => true, 'auto_increment' => true),
			'site_id' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 1),
			'check_date' => array('type' => 'INT', 'unsigned' => true, 'default' => 0)
		));

		ee()->dbforge->add_key('id', true);
		ee()->dbforge->add_key('site_id');
		ee()->dbforge->add_key('check_date');

		ee()->dbforge->create_table('scheduled_trigger', true);

		// Scheduled Trigger queue

		ee()->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => true, 'auto_increment' => true),
			'site_id' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 1),
			'entry_id' => array('type' => 'INT', 'unsigned' => true, 'default' => 0),
			'type' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 1)
		));

		ee()->dbforge->add_key('id', true);
		ee()->dbforge->add_key('site_id');
		ee()->dbforge->add_key('entry_id');

		ee()->dbforge->create_table('scheduled_trigger_queue', true);

		// Scheduled Trigger log

		ee()->dbforge->add_field(array(
			'id' => array('type' => 'INT', 'unsigned' => true, 'auto_increment' => true),
			'site_id' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 1),
			'entry_id' => array('type' => 'INT', 'unsigned' => true, 'default' => 0),
			'type' => array('type' => 'TINYINT', 'unsigned' => true, 'default' => 1),
			'triggered' => array('type' => 'INT', 'unsigned' => true, 'default' => 1)
		));

		ee()->dbforge->add_key('id', true);
		ee()->dbforge->add_key('site_id');
		ee()->dbforge->add_key('entry_id');

		ee()->dbforge->create_table('scheduled_trigger_log', true);

		return true;
	}

	/**
	 * Uninstall
	 *
	 * @return boolean
	 */
	public function uninstall()
	{
		ee()->load->dbforge();
		
		$query = ee()->db->get_where('modules', array(
			'module_name' => $this->name
		));

		// Remove module permissions

		if ($query->row('module_id'))
		{
			ee()->db->delete('module_member_groups', array(
				'module_id' => $query->row('module_id')
			));
		}

		// Remove module

		ee()->db->delete('modules', array(
			'module_name' => 'Scheduled_trigger'
		));

		// Remove actions

		ee()->db->delete('actions', array(
			'class' => 'Scheduled_trigger'
		));

		// Remove Scheduled Trigger tables

		ee()->dbforge->drop_table('scheduled_trigger');
		ee()->dbforge->drop_table('scheduled_trigger_queue');
		ee()->dbforge->drop_table('scheduled_trigger_log');

		return true;
	}

	/**
	 * Update
	 *
	 * @return boolean
	 */
	public function update($current = '')
	{
		// Version comparison

		if ($current == $this->version)
		{
			return false;
		}

		return true;
	}
}