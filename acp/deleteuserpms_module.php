<?php
/**
*
* @package Delete User PMs Extension
* @copyright (c) 2022 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\deleteuserpms\acp;

class deleteuserpms_module
{
	public $u_action;

	function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name		= 'delete_user_pms';
		$this->page_title	= $phpbb_container->get('language')->lang('DELETE_USER_PMS');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('david63.deleteuserpms.admin.controller');

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		$admin_controller->display_output();
	}
}
