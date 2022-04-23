<?php
/**
*
* @package Delete User PMs Extension
* @copyright (c) 2022 david63
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

/**
 * DEVELOPERS PLEASE NOTE
 *
 * All language files should use UTF-8 as their encoding and the files must not contain a BOM.
 *
 * Placeholders can now contain order information, e.g. instead of
 * 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
 * translators to re-order the output of data while ensuring it remains correct
 *
 * You do not need this where single placeholders are used, e.g. 'Message %d' is fine
 * equally where a string contains only two placeholders which are used to wrap text
 * in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
 *
 * Some characters you may want to copy&paste:
 * ’ » “ ” …
 *
 */

$lang = array_merge($lang, array(
	'CANNOT_DELETE_FOUNDER_PMS'		=> 'Cannot delete PM data for a Founder.',

	'DELETE_PM_CONFIRM'				=> 'Are you sure you wish to delete the PM data for <strong>%1$s</strong>?',
	'DELETE_USER_PMS_EXPLAIN'		=> 'Delete all PM data (PMs, folders and rules) for a selected user.',

	'MAKE_USER_INACTIVE'			=> 'Make user inactive',
	'MAKE_USER_INACTIVE_EXPLAIN'	=> 'After deleting the user’s PM data make this user inactive.',

	'NO_USER_DATA'					=> 'The selected user has no PM data to delete.',
	'NO_USER_SPECIFIED'				=> 'No username was specified.',

	'PM_DATA_DELETED'				=> 'PM data was successfully deleted for %1$s.',
	'PM_DATA_DELETED_INACTIVE'		=> 'User %1$s was successfully made inactive after the PM data was deleted.',

	'USER_DETAILS'					=> 'User details',
	'USER_EXPLAIN'					=> 'Select the user whose PM data you wish to delete.',
));
