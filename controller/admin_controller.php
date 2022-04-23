<?php
/**
 *
 * @package Delete User PMs Extension
 * @copyright (c) 2022 david63
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace david63\deleteuserpms\controller;

use phpbb\db\driver\driver_interface;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbb\log\log;
use phpbb\language\language;
use david63\deleteuserpms\core\functions;

/**
 * Admin controller
 */
class admin_controller
{
	/** @var driver_interface */
	protected $db;

	/** @var request */
	protected $request;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var \phpbb\log */
	protected $log;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string PHP extension */
	protected $phpEx;

	/** @var language */
	protected $language;

	/** @var functions */
	protected $functions;

	/** @var array phpBB tables */
	protected $tables;

	/** @var string */
	protected $ext_images_path;

	/** @var string custom constants */
	protected $dpmconstants;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor for admin controller
	 *
	 * @param driver_interface  $db                 The db connection
	 * @param request           $request            Request object
	 * @param template          $template           Template object
	 * @param language          $language           Language object
	 * @param functions         $functions          Functions for the extension
	 * @param array             $tables             phpBB db tables
	 * @param string            $ext_images_path    Path to this extension's images
	 * @param array             $dpmconstants       Cusom constants
	 *
	 * @return \david63\deleteuserpms\controller\admin_controller
	 *
	 * @access public
	 */
	public function __construct(driver_interface $db, request $request, template $template, user $user, log $log, string $root_path, string $php_ext, language $language, functions $functions, array $tables, string $ext_images_path, array $dpmconstants)
	{
		$this->db              = $db;
		$this->request         = $request;
		$this->template        = $template;
		$this->user            = $user;
		$this->log             = $log;
		$this->root_path       = $root_path;
		$this->phpEx           = $php_ext;
		$this->language        = $language;
		$this->functions       = $functions;
		$this->tables          = $tables;
		$this->ext_images_path = $ext_images_path;
		$this->constants       = $dpmconstants;
	}

	/**
	 * Display the output for this extension
	 *
	 * @return null
	 * @access public
	 */
	public function display_output()
	{
		// Add the language files
		$this->language->add_lang(['acp_deleteuserpms', 'acp_common'], $this->functions->get_ext_namespace());

		add_form_key($this->constants['form_key']);

		// Define the variables
		$action      	= $this->request->variable('action', '');
		$back 			= false;
		$delete      	= ($action == 'delete') ? true : false;
		$delete_data 	= $this->request->variable('delete_data', 0);
		$errors 		= [];
		$make_inactive	= $this->request->variable('make_user_inactive', '');
		$pm_username	= utf8_normalize_nfc($this->request->variable('pm_username', '', true));
		$submit      	= ($this->request->is_set_post('submit')) ? true : false;

		if ($submit)
		{
			if (!check_form_key($this->constants['form_key']))
			{
				trigger_error($this->language->lang('FORM_INVALID'));
			}
		}

		if ($submit || $delete)
		{
			// Do we have a user?
			if (!empty($pm_username))
			{
				// Make sure this isn't a founder
				$user_type = $this->get_user_info($pm_username, 'user_type');

				if ($user_type == USER_FOUNDER)
				{
					$errors[] = $this->language->lang('CANNOT_DELETE_FOUNDER_PMS');
				}

				// Do we have any data to delete?
				$delete_data = $this->get_pm_data($this->get_user_info($pm_username, 'user_id'));

				if (!$delete_data)
				{
					$errors[] = $this->language->lang('NO_USER_DATA');
				}
			}
			else
			{
				$errors[] = $this->language->lang('NO_USER_SPECIFIED');
			}

			// No errors?
			if (empty($errors))
			{
				if (confirm_box(true))
				{
					$this->delete_pm_data($this->get_user_info($pm_username, 'user_id'), $delete_data, $make_inactive);
					$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_USER_PM_DELETED', time(), [$pm_username]);
					if ($make_inactive)
					{
						$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_USER_INACTIVE',  time(), [$pm_username]);
						trigger_error($this->language->lang('PM_DATA_DELETED_INACTIVE', $pm_username) . adm_back_link($this->u_action));
					}
					else
					{
						trigger_error($this->language->lang('PM_DATA_DELETED', $pm_username) . adm_back_link($this->u_action));
					}
				}
				else
				{
					$hidden_fields = [
						'action' 				=> 'delete',
						'delete_data' 			=> $delete_data,
						'make_user_inactive'	=> $make_inactive,
						'pm_username'			=> $pm_username,
					];

					confirm_box(false, sprintf($this->language->lang('DELETE_PM_CONFIRM'), $pm_username), build_hidden_fields($hidden_fields));
				}
			}
		}

		// Are the PHP and phpBB versions valid for this extension?
		$valid = $this->functions->ext_requirements();

		// Template vars for header panel
		$version_data = $this->functions->version_check();

		$this->template->assign_vars([
			'DOWNLOAD' 			=> (array_key_exists('download', $version_data)) ? '<a class="download" href =' . $version_data['download'] . '>' . $this->language->lang('NEW_VERSION_LINK') . '</a>' : '',

			'EXT_IMAGE_PATH' 	=> $this->ext_images_path,

			'ERROR_TITLE' 		=> $this->language->lang('WARNING'),
			'ERROR_DESCRIPTION'	=> implode('<br>', $errors),

			'HEAD_TITLE' 		=> $this->language->lang('DELETE_USER_PMS'),
			'HEAD_DESCRIPTION' 	=> $this->language->lang('DELETE_USER_PMS_EXPLAIN'),

			'NAMESPACE' 		=> $this->functions->get_ext_namespace('twig'),

			'PHP_VALID' 		=> $valid[0],
			'PHPBB_VALID' 		=> $valid[1],

			'S_BACK' 			=> $back,
			'S_ERROR' 			=> (!empty($errors)) ? true : false,
			'S_VERSION_CHECK' 	=> (array_key_exists('current', $version_data)) ? $version_data['current'] : false,

			'VERSION_NUMBER' 	=> $this->functions->get_meta('version'),
		]);

		$this->template->assign_vars([
			'PM_USERNAME' 		=> (!empty($user_id)) ? $pm_username : '',

			'U_ACTION' 			=> $this->u_action,
			'U_FIND_USERNAME'	=> append_sid("{$this->root_path}memberlist.$this->phpEx", 'mode=searchuser&amp;form=deleteuserpms&amp;field=pm_username&amp;select_single=true'),
		]);
	}

	/**
	 * Get the user's PM data
	 *
	 * @param	int $user_id
	 * @return	$pm_data
	 * @access	public
	 */
	public function get_pm_data($user_id)
	{
		$pm_data = 0;

		// Has the user sent any PMs?
		$sql = 'SELECT user_id
            FROM ' . $this->tables['privmsgs_to'] . '
            WHERE user_id = ' . $user_id;

		$result  = $this->db->sql_query($sql);
		$pm_data = ($result->num_rows > 0) ? phpbb_optionset($this->constants['pm_to'], true, $pm_data) : $pm_data;

		$this->db->sql_freeresult($result);

		// Has the user any PMs?
		$sql = 'SELECT author_id
            FROM ' . $this->tables['privmsgs'] . '
            WHERE author_id = ' . $user_id;

		$result  = $this->db->sql_query($sql);
		$pm_data = ($result->num_rows > 0) ? phpbb_optionset($this->constants['pms'], true, $pm_data) : $pm_data;

		$this->db->sql_freeresult($result);

		// Does the user have any PM folders?
		$sql = 'SELECT user_id
            FROM ' . $this->tables['privmsgs_folder'] . '
            WHERE user_id = ' . $user_id;

		$result  = $this->db->sql_query($sql);
		$pm_data = ($result->num_rows > 0) ? phpbb_optionset($this->constants['pm_folder'], true, $pm_data) : $pm_data;

		$this->db->sql_freeresult($result);

		// Does the user have any PM rules?
		$sql = 'SELECT user_id
            FROM ' . $this->tables['privmsgs_rules'] . '
            WHERE user_id = ' . $user_id;

		$result  = $this->db->sql_query($sql);
		$pm_data = ($result->num_rows > 0) ? phpbb_optionset($this->constants['pm_rules'], true, $pm_data) : $pm_data;

		$this->db->sql_freeresult($result);

		return $pm_data;
	}

	/**
	 * Delete the user's PMs
	 *
	 * @param	int $user_id
	 * @parm	int	$delete_data
	 * @return	null
	 * @access	public
	 */
	public function delete_pm_data($user_id, $delete_data, $make_inactive)
	{
		// Delete any sent PMs for the user
		if (phpbb_optionget($this->constants['pm_to'], $delete_data))
		{
			$sql = 'DELETE FROM ' . $this->tables['privmsgs_to'] . '
				WHERE user_id = ' . $user_id;

			$this->db->sql_query($sql);
		}

		// Delete any PMs for the user
		if (phpbb_optionget($this->constants['pms'], $delete_data))
		{
			$sql = 'DELETE FROM ' . $this->tables['privmsgs'] . '
				WHERE author_id = ' . $user_id;

			$this->db->sql_query($sql);
		}

		// Delete any PM folders for the user
		if (phpbb_optionget($this->constants['pm_folder'], $delete_data))
		{
			$sql = 'DELETE FROM ' . $this->tables['privmsgs_folder'] . '
				WHERE user_id = ' . $user_id;

			$this->db->sql_query($sql);
		}

		// Delete any PM rules for the user
		if (phpbb_optionget($this->constants['pm_rules'], $delete_data))
		{
			$sql = 'DELETE FROM ' . $this->tables['privmsgs_rules'] . '
				WHERE user_id = ' . $user_id;

			$this->db->sql_query($sql);
		}

		if ($make_inactive)
		{
			if (!function_exists('user_active_flip'))
			{
				include($this->root_path  . 'includes/functions_user.' . $this->phpEx);
			}
			user_active_flip('deactivate', $user_id);
		}
	}

	/**
	 * Get user info from username
	 *
	 * @param	string $pm_username
	 * @return	$user_id/$user_type
	 * @access	public
	 */
	public function get_user_info($pm_username, $data_type)
	{
		$sql = 'SELECT user_id, user_type
            FROM ' . $this->tables['users'] . "
            WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($pm_username)) . "'";

		$result  	= $this->db->sql_query($sql);
		$row 		= $this->db->sql_fetchrow($result);
		$user_id 	= $row['user_id'];
		$user_type	= $row['user_type'];

		$this->db->sql_freeresult($result);

		return ($data_type == 'user_id') ? $user_id : $user_type;
	}

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 *
	 * @return null
	 * @access public
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
