<?php
/**
* gloss.php
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015-2016 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge ($lang, array(
	'LGLOSSAIRE'	=> 'Glossary',
	'TGLOSSAIRE'	=> 'Glossary',
	// UCP
	'UCP_GLOSS_TITLE'	=> 'Glossary',
	'UCP_GLOSS'		=> 'Main page',
     'UCP_CONFIG_SAVED'	=> 'User configuration updated successfully<br /><br />%sClick here to return to the previous page.%s',
	'UCP_ENABLE'		=> 'Enable the glossary feature',
	'UCP_ENABLE_EXPLAIN' => 'The glossary displays an explanation and optionally a picture when you click on technical terms in the posts.',
	     
// Installation
	'ROLE_GLOSS_ADMIN'	=> 'Glossary Administrators',
	'ROLE_GLOSS_EDITOR'	=> 'Glossary Editors',
	'ROLE_DESCRIPTION_GLOSS_ADMIN' => 'Administration role to manage the glossary and its editors','ROLE_DESCRIPTION_GLOSS_EDITOR' => 'User role to be assigned for editing the glossary',
	
// ACP
	'ACP_GLOSS_TITLE'	=> 'Glossary',
	'ACP_GLOSS'		=> 'Settings',
	'ALLOW_FEATURE'		=> 'Enable Glossary Feature',
     'ALLOW_FEATURE_EXPLAIN'	=> 'You may enable/disable the glossary tagging feature for the whole board. Each user can disable the tagging feature in posts from the user control panel.',
	'ALLOW_TITLE'		=> 'Enable Tooltip',
     'ALLOW_TITLE_EXPLAIN'	=> 'You may enable/disable the display of the term description in a tooltip when hovering over the term. Long descriptions will be truncated at 50 characters.',
	'CREATE_UGROUP'		=> 'Creation of an usergroup',
	'CREATE_UGROUP_EXPLAIN'	=> 'You may create an usergroup and assign to it the glossary editor role created when installing the extension. You may then add users to this group.',
	'CREATE_AGROUP'		=> 'Creation of an administrator group',
	'CREATE_AGROUP_EXPLAIN'	=> 'You may create a group to manage the glossary administrators. You may then add administrators to this group.',
	'LANGUAGE'		=> 'Default language',
	'LANGUAGE_EXPLAIN'	=> 'Language code (board language by default) which will be registered in the base for the glossary term if you don\'t specify another language in the edition form.',
	'PIXELS'			=> 'Size of uploaded pictures in pixels',
	'PIXELS_EXPLAIN'	=> 'Set here the size (in pixels) of the long side of the uploaded pictures.',
	'POIDS'			=> 'Size in kB',
	'POIDS_EXPLAIN'	=> 'Set here the maximum size (in kB) of the uploaded pictures.',
	
 ));
