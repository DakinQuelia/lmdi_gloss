<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\migrations;

// For the function create_group()
global $phpbb_root_path, $phpEx;
include($phpbb_root_path . '/includes/functions_user.' . $phpEx);

class release_1 extends \phpbb\db\migration\migration
{

	public function effectively_installed()
	{
		return isset($this->config['lmdi_glossary']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
	}

	public function update_data()
	{
		return array(
			// Add configuration rows
			array('config.add', array('lmdi_glossary', 1)),
			array('config.add', array('lmdi_glossary_ucp', 0)),
			array('config.add', array('lmdi_glossary_title', 0)),
			
			// Insertion of dummy entries
			array('custom', array(array(&$this, 'insert_sample_data'))),
			
			// Modify collation setting of the glossary table
			array('custom', array(array(&$this, 'utf8_unicode_ci'))),
			
			// Creation of a group for the editors of the glossary table
			array('custom', array(array(&$this, 'group_creation'))),
			
			// Role addition
			// array('permission.role_add', array('lmdi_glossary', 'u_', 'GLOSSARY EDITOR')),
			// array('permission.role_add', array('GLOSSARY_EDITOR', 'u_', 'Editor of glossary entries')),
			
			// Add permissions (set = true, unset = false)
			array('permission.add', array('a_lmdi_glossary', true, 'a_')),
			array('permission.add', array('u_lmdi_glossary', true, 'u_')),
			
			// Set permissions
			// array('permission.permission_set', array('REGISTERED', 'u_lmdi_glossary', 'group')),
			array('permission.permission_set', array('ADMINISTRATORS', 'a_lmdi_glossary', 'group')),
			array('permission.permission_set', array('GLOSSARY', 'u_lmdi_glossary', 'group')),
				
			// ACP modules
			array('module.add', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GLOSS_TITLE'
			)),
			
			array('module.add', array(
				'acp',
				'ACP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\acp\gloss_module',
					'modes'			=> array('settings'),
				),
			)),
			
			// UCP modules
			array('module.add', array(
				'ucp',
				'0',
				'UCP_GLOSS_TITLE',
			)),
			
			array('module.add', array(
				'ucp',
				'UCP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\ucp\ucp_gloss_module',
					'module_mode'		=> array('settings'),
					'module_auth'       => 'ext_lmdi/gloss',
					'module_display'	=> 0,
					'module_enabled'	=> 0,
					'module_class'		=> 'ucp',
				),
			)),
		);
	}
	
	public function group_creation()
	{
		// global $user;
		// $user->add_lang_ext('lmdi/gloss', 'gloss');
		// $str_desc = $this->user->lang['GLOSS_GROUP_DESC'];
		$str_desc = "Glossary Editor Group";
		
		// Glossary group creation
		$group_id = '';
		$group_type = 0;
		$group_name = "GLOSSARY";
		$group_desc = $str_desc;

		$group_attributes = array(
		    'group_colour' => '00FFFF',
		    'group_rank' => 0,
		    'group_avatar' => 0,
		    'group_avatar_type' => 0,
		    'group_avatar_width' => 0,
		    'group_avatar_height' => 0,
		    'group_legend' => 0,
		    'group_receive_pm' => 1,
		);    
		// In file includes/functions_user.php
		$group = group_create($group_id, $group_type, $group_name, $group_desc, $group_attributes);
		var_dump ($group);
	}
	
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'lmdi_gloss' => array('BOOL', 1),
				),
			),
			'add_tables'   => array(
				$this->table_prefix . 'glossary'   => array(
					'COLUMNS'   => array(
						'term_id'	=> array ('UINT', null, 'auto_increment'),
						'variants'	=> array ('VCHAR:80', ''),
						'term'	=> array ('VCHAR:80', ''),
						'description'	=> array ('VCHAR:512', ''),
						'picture'	=> array ('VCHAR:80', ''),
						'lang'	=> array ('VCHAR:15', 'fr'),
					),
				'PRIMARY_KEY'   => 'term_id',
				),
			),
		);
	}
	
	public function utf8_unicode_ci()
	{
		$sql = "alter table phpbb3_glossary convert to character set utf8 collate utf8_unicode_ci";
		$this->db->sql_query($sql);
	}
	
	
	public function insert_sample_data()
	{
		global $user;
		// Define sample data
		$sample_data = array(
				array (
					'variants' => 'test, tests, tested',
					'term' => 'Test',
					'description' => 'Test definition, etc.',
					'picture' => 'nopict',
					'lang' => 'en',
				),
				array (
					'variants' => 'try, demo, trial',
					'term' => 'Test2',
					'description' => 'Second test definition, etc.',
					'picture' => 'nopict',
					'lang' => 'en',
				),
			);
		// Insert sample data
		$this->db->sql_multi_insert($this->table_prefix . 'glossary', $sample_data);
	}


    	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'lmdi_gloss',
				),
			),
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('lmdi_glossary')),
			array('config.remove', array('lmdi_glossary_ucp')),
			array('config.remove', array('lmdi_glossary_title')),
			
			array('module.remove', array(
				'acp',
				'ACP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\acp\gloss_module',
					'modes'			=> array('settings'),
				),
			)),
			
			array('module.remove', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GLOSS_TITLE'
			)),
						
			// Role suppression
			// array('permission.role_remove', array('GLOSSARY_EDITOR')),
			
			// Remove permissions
			array('permission.remove', array('a_lmdi_glossary')),
			array('permission.remove', array('u_lmdi_glossary')),
			
			// Unset permissions
			// array('permission.permission_unset', array('REGISTERED', 'u_lmdi_glossary', 'group')),
			array('permission.permission_unset', array('ADMINISTRATORS', 'a_lmdi_glossary', 'group')),
			array('permission.permission_unset', array('GLOSSARY', 'u_lmdi_glossary', 'group')),
			
			/*
			array('module.remove', array(
				'ucp',
				'UCP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\ucp\ucp_psb_module',
					'module_mode'		=> array('settings'),
					'module_auth'       => 'ext_lmdi/gloss',
					'module_display'	=> 0,
					'module_enabled'	=> 1,
					'module_class'		=> 'ucp',
				),
			)),
			
			array('module.remove', array(
				'ucp',
				'0',
				'UCP_GLOSS_TITLE',
			)),
			*/
		);
	}
	
}
