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
	'TGLOSSAIRE'	=> 'Glossary',
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
	'GLOSS_ED_PIEXPL'	=> 'Name of the picture file, without its extension, which must me *.jpg. The file must be in the folder ext/lmdi/gloss/glossaire.',
	'GLOSS_ED_ACT'		=> 'Action',
	'GLOSS_ED_EXPL'	=> 'An edition link exists in the Action column for each entry. To create a new entry, click ',
	'GLOSS_ED_ICI'		=> '<b>here</b>',
	'GLOSS_ED_EDIT'	=> 'Edit',
	'GLOSS_LANG'		=> 'Language',
	'UPLOAD_FILE'		=> 'Picture to upload from your computer',
	// Installation
	'ROLE_GLOSS_ADMIN'	=> 'Glossary Administrators',
	'ROLE_GLOSS_EDITOR'	=> 'Glossary Editors',
	'ROLE_DESCRIPTION_GLOSS_ADMIN'		=> 'Administration role to manage the glossary and its editors','ROLE_DESCRIPTION_GLOSS_EDITOR'		=> 'User role to be assigned for editing the glossary',
	
// ACP
	'ACP_GLOSS_TITLE'	=> 'Glossary',
     'GLOSS_PAGE'		=> 'Glossary',
	'ACP_LEXICON'	=> 'Glossary',
	'ACP_LEXICON_EXPLAIN'	=> 'Glossary settings',
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
	'LANGUAGE_EXPLAIN'	=> 'Language code (board language by default) which will be registered in the base for the glossary term if you don\'t specify another language in the editing form.',
	'PIXELS'			=> 'Size of uploaded pictures in pixels',
	'PIXELS_EXPLAIN'	=> 'Set here the size (in pixels) of the long side of the uploaded pictures.',
	'PX'				=> 'px',
	'POIDS'			=> 'Size in kB',
	'POIDS_EXPLAIN'	=> 'Set here the maximum size (in kB) of the uploaded pictures.',
	'KB'				=> 'kB',
	'LMDI_GLOSS_DISALLOWED_CONTENT'	=> 'Upload has been interrupted because the file had been identified as a potential threat.',
	'LMDI_GLOSS_DISALLOWED_EXTENSION'	=> 'The file extension <strong>%s</strong> is not allowed.',
	'LMDI_GLOSS_EMPTY_FILEUPLOAD'		=> 'The file is empty.',
	'LMDI_GLOSS_EMPTY_REMOTE_DATA'	=> 'The file data seem incorrect or corrupted.',
	'LMDI_GLOSS_IMAGE_FILETYPE_MISMATCH'	=> 'File type mismatch: expected extension %1$s but extension %2$s found.',
	'LMDI_GLOSS_INVALID_FILENAME'		=> '%s is an invalid file name.',
	'LMDI_GLOSS_NOT_UPLOADED'		=> 'The file could not be uploaded',
	'LMDI_GLOSS_PARTIAL_UPLOAD'		=> 'The file was not completly transfered.',
	'LMDI_GLOSS_PHP_SIZE_NA'			=> 'The file size is too high.<br />The maximum size set in php.ini could not be determined".',
	'LMDI_GLOSS_PHP_SIZE_OVERRUN'		=> 'The file size is too high. The maximum size allowed is %d Mo.<br />Please note that this value is set in php.ini and cannot be outreached.',
	'LMDI_GLOSS_REMOTE_UPLOAD_TIMEOUT'	=> 'The specified file could not be uploaded because the request timed out.',
	'LMDI_GLOSS_UNABLE_GET_IMAGE_SIZE'	=> 'It was not possible to determine the file dimensions',
	'LMDI_GLOSS_WRONG_FILESIZE'		=> 'The file size must be below %1d kB.',
	'LMDI_GLOSS_WRONG_SIZE'			=> 'The specified file has a width of %3$d pixels and a height of %4$d pixels. The banner cannot be over %1$d pixels width and %2$d height.',
	'LMDI_CLICK_BACK'				=> 'Click <a href="javascript:history.go(-1);"><b>here</b></a> to come back to the edition form.',
// UCP
	'UCP_GLOSS_TITLE'	=> 'Glossary',
     'UCP_GLOSS_MANAGE'	=> 'Glossary',
	'UCP_CONFIG_SAVED'	=> 'User configuration updated successfully<br /><br />%sClick here to return to the previous page.%s',
	'UCP_GLOSS'		=> 'Main page',
	'UCP_ENABLE'		=> 'Enable the glossary feature',
	'UCP_ENABLE_EXPLAIN' => 'The glossary displays an explanation and eventually a picture when you click on technical terms in the posts.',
	
 ));
