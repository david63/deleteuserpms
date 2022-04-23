<?php
/**
*
* @package Delete User PMs Extension
* @copyright (c) 2022 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace david63\deleteuserpms\migrations;

class version_1_0_0 extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v330\v330'];
	}

	public function update_data()
	{
		$update_data = [];

		if ($this->module_check())
		{
			$update_data[] = array('module.add', array('acp', 'ACP_CAT_USERGROUP', 'ACP_USER_UTILS'));
		}

		$update_data[] = array('module.add', array(
			'acp', 'ACP_USER_UTILS', array(
				'module_basename'	=> '\david63\deleteuserpms\acp\deleteuserpms_module',
				'modes'				=> array('main'),
			),
		));

		return $update_data;
	}

	protected function module_check()
	{
		$sql = 'SELECT module_id
				FROM ' . $this->table_prefix . "modules
    			WHERE module_class = 'acp'
        			AND module_langname = 'ACP_USER_UTILS'
        			AND right_id - left_id > 1";

		$result		= $this->db->sql_query($sql);
		$module_id	= (int) $this->db->sql_fetchfield('module_id');
		$this->db->sql_freeresult($result);

		// return true if module is empty, false if has children
		return (bool) !$module_id;
	}
}
