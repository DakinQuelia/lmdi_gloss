<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\migrations;

class release_1 extends \phpbb\db\migration\migration
{
	
	var 	$str_desc = "Glossary Editor Group";


	public function effectively_installed()
	{
		return isset($this->config['lmdi_glossary']);
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\alpha2');
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
	
	public function update_data()
	{
		return array(
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

			// Three configuration rows
			array('config.add', array('lmdi_glossary', 1)),
			array('config.add', array('lmdi_glossary_ucp', 0)),
			array('config.add', array('lmdi_glossary_title', 0)),
			
			// Modify collation setting of the glossary table
			array('custom', array(array(&$this, 'utf8_unicode_ci'))),
			
			// Insertion of dummy entries in the glossary table
			array('custom', array(array(&$this, 'insert_sample_data'))),
			
			// Creation of a group for the editors of the glossary
			// array('custom', array(array(&$this, 'group_creation'))),
			
			// Add a permission (set = true, unset = false)
			array('permission.add', array('u_lmdi_glossary', false, 'u_')),

			// Role addition
			array('permission.role_add', array('ROLE_GLOSSARY_EDITOR', 'u_', 'Editor of glossary entries')),
			
			// Set permissions
			array('permission.permission_set', array('ROLE_GLOSSARY_EDITOR', 'u_lmdi_glossary', 'role')),
				
		);
	}
	
	public function revert_data()
	{
		return array(
			array('config.remove', array('lmdi_glossary')),
			array('config.remove', array('lmdi_glossary_ucp')),
			array('config.remove', array('lmdi_glossary_title')),
			
			/*
			array('module.remove', array(
				'acp',
				'ACP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\acp\gloss_module',
					'modes'			=> array('settings'),
				),
			)),
			*/
			
			array('module.remove', array(
				'acp',
				'ACP_CAT_DOT_MODS',
				'ACP_GLOSS_TITLE'
			)),
						
			// Deletion of the group for the editors of the glossary
			array('custom', array(array(&$this, 'group_deletion'))),
			
			// Unset permissions
			array('permission.permission_unset', array('ROLE_GLOSSARY_EDITOR', 'u_lmdi_glossary', 'role')),
			
			// Remove permissions
			array('permission.remove', array('u_lmdi_glossary')),
			
			// Role suppression
			array('permission.role_remove', array('ROLE_GLOSSARY_EDITOR')),
			
			/*
			array('module.remove', array(
				'ucp',
				'UCP_GLOSS_TITLE',
				array(
					'module_basename'	=> '\lmdi\gloss\ucp\ucp_gloss_module',
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
	
    	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'lmdi_gloss',
				),
			),
			'drop_tables'   => array(
				$this->table_prefix . 'glossary',
			),
		);
	}

	public function utf8_unicode_ci()
	{
		global $table_prefix;
		$sql = "alter table ${table_prefix}glossary convert to character set utf8 collate utf8_unicode_ci";
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
		$group_desc = $this->str_desc;

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
		// var_dump ($group);
	}
	
	public function group_deletion()
	{
		global $table_prefix;
		$sql = "SELECT group_id from ${table_prefix}groups where group_name = 'GLOSSARY'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow ($result);
		$group_id = $row['group_id'];
		$this->db->sql_freeresult ($result);
		if ($group_id) 
		{
			group_delete($group_id, 'GLOSSARY');
		}
	}
	
}
