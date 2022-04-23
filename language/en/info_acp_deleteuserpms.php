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
	'ACP_USER_UTILS'		=> 'User utilities',

	'DELETE_USER_PMS'		=> 'Delete user PM data',

	'LOG_USER_PM_DELETED'	=> '<strong>PM data deleted</strong> » %1$s',
	'LOG_USER_INACTIVE'		=> '<strong>User made inactive via PM data delete</strong><br>%1$s » %2$s',
));
