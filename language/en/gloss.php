<?php
/**
* gloss.php
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015 LMDI - Pierre Duhem
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
// Static Glossary page
   	'LGLOSSAIRE'	=> 'Glossary',
	'TGLOSSAIRE'	=> 'Entomological glossary',
	'ILLUSTRATION'	=>  "Some terms have an explicative illustration.<br />In such a case, there is a link at the end of the row.<br />Click on it to display the picture.<br />Click on the picture again to come back.",
	'GLOSS_DISPLAY'	=> 'Display',
	'GLOSS_CLICK'		=> 'Click on the picture to come back to previous page.',
	'GLOSS_VIEW'		=> 'Glossary Viewer',
	'GLOSS_BIBLIO'		=> "
		<p class=\"m\">
		<u>Bibliography</u><br /> 
		<br /><br /> 
		<u>Webography</u><br /><br />
		<br />
		<u>Illustrations</u><br /><br />
		<br /> 
		</p>",
// Glossary edition page
	'GLOSS_EDIT'	=>'Glossary Item Edition',
	'GLOSS_CREAT'	=>'Glossary Item Creation',
	'GLOSS_VARIANTS' => 'Terms to search in the posts',
	'GLOSS_VARIANTS_EX' => 'One or several terms, separated by a comma.',
	'GLOSS_TERM'	=> 'Term displayed',
	'GLOSS_TERM_EX' => 'Term to use as title in the popup window.',
	'GLOSS_DESC'	=> 'Definition of term',
	'GLOSS_PICT'	=> 'Picture',
	'GLOSS_REGIS'	=> 'Save',
	'GLOSS_SUPPR'	=> 'Delete',
	'GLOSS_EDITION'	=> 'Glossary Edition Page',
	'GLOSS_ED_TERM'	=> 'Term',
	'GLOSS_ED_DEF'		=> 'Definition',
	'GLOSS_ED_PICT'	=> 'Picture',
	'GLOSS_ED_PIEXPL'	=> 'Type the name of the picture file, without its ending, which must me *.jpg. Upload the file to the folder ext/lmdi/gloss/glossaire.',
	'GLOSS_ED_ACT'		=> 'Action',
	'GLOSS_ED_EXPL'	=> 'An edition link exists in the Action column for each entry. To create a new entry, click ',
	'GLOSS_ED_ICI'		=> 'here',
	'GLOSS_ED_EDIT'	=> 'Edit',
// ACP
	'ACP_GLOSS_TITLE'	=> 'Glossary',
     'GLOSS_PAGE'		=> 'Glossary',
	'ACP_LEXICON'	=> 'Entomological Glossary',
	'ACP_LEXICON_EXPLAIN'	=> 'Glossary settings',
	'ACP_GLOSS_TITLE'	=> 'Entomological Glossary',
	'ACP_GLOSS'		=> 'Settings',
	'ALLOW_FEATURE'		=> 'Enable Glossary Feature',
     'ALLOW_FEATURE_EXPLAIN'	=> 'You may enable/disable the feature for the whole board. Each user can disable the glossary feature in posts from the user control panel.',
	'ALLOW_TITLE'		=> 'Enable Tooltip',
     'ALLOW_TITLE_EXPLAIN'	=> 'You may enable/disable the display of the term description in a tooltip when hovering over the term. Long description will be truncated at 50 characters.',
	'GLOSS_GROUP_DESC'		=> 'Group of Glossary editors',
// UCP
	'UCP_GLOSS_TITLE'	=> 'Entomological Glossary',
     'UCP_GLOSS_MANAGE'	=> 'Entomological Glossary',
	'UCP_CONFIG_SAVED'	=> 'User configuration updated successfully<br /><br />%sClick here to return to the download settings%s',
	'UCP_GLOSS'		=> 'Main page',
	'UCP_ENABLE'		=> 'Enable the glossary feature',
	'UCP_ENABLE_EXPLAIN' => 'The glossary displays an explanation and eventually a picture when you click on technical terms in the posts.',
	
 ));
