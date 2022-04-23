<?php
/**
*
* @package Delete User PMs Extension
* @copyright (c) 2022 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\deleteuserpms\acp;

class deleteuserpms_info
{
	function module()
	{
		return array(
			'filename'	=> '\david63\deleteuserpms\acp\deleteuserpms_module',
			'title'		=> 'DELETE_USER_PMS',
			'modes'		=> array(
				'main'		=> array('title' => 'DELETE_USER_PMS', 'auth' => 'ext_david63/deleteuserpms && acl_a_user', 'cat' => array('ACP_CAT_USERS')),
			),
		);
	}
}
