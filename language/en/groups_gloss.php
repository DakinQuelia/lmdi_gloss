<?php
/**
*
* gloss.php
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine


$lang = array_merge($lang, array(
	'GROUP_GLOSS_ADMIN' => 'Glossary Administrators',
	'GROUP_DESCRIPTION_GLOSS_ADMIN'	=> 'Usergroup of Glossary administrators',
	'GROUP_GLOSS_EDITOR'		=> 'Glossary Editors',
	'GROUP_DESCRIPTION_GLOSS_EDITOR'	=> 'Usergroup of Glossary editors',
));
