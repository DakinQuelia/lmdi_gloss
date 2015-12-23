<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\migrations;

use \phpbb\db\migration\container_aware_migration;

global $phpbb_root_path;
include ($phpbb_root_path . 'includes/functions_user.php');

class release_1 extends container_aware_migration
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
		// To get the $user array
		$user = $this->container->get('user');
		// Load the lang file
		$user->add_lang_ext('lmdi/gloss', 'gloss');
		$titre_role_a = $user->lang('ROLE_A_LMDI_GLOSSARY');
		$titre_role_u = $user->lang('ROLE_U_LMDI_GLOSSARY');
		$descr_role_a = $user->lang('ROLE_A_LMDI_DESC');
		$descr_role_u = $user->lang('ROLE_U_LMDI_DESC');
		
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
			array('config.add', array('lmdi_glossary_usergroup', 0)),
			array('config.add', array('lmdi_glossary_admingroup', 0)),
			
			
			// Modify collation setting of the glossary table
			array('custom', array(array(&$this, 'utf8_unicode_ci'))),
			
			// Insertion of dummy entries in the glossary table
			array('custom', array(array(&$this, 'insert_sample_data'))),
			
			// Creation of a group for the editors of the glossary
			// array('custom', array(array(&$this, 'group_creation'))),
			
			
			// Add roles
			array('permission.role_add', array($titre_role_a, 'a_', $descr_role_a)),
			array('permission.role_add', array($titre_role_u, 'u_', $descr_role_u)),

			// Add permissions (global = true, local = false)
			array('permission.add', array('a_lmdi_glossary', true)),
			array('permission.add', array('u_lmdi_glossary', true)),

			// Assign permissions to the roles
			array('permission.permission_set', array($titre_role_a, 'a_lmdi_glossary', 'role')),
			array('permission.permission_set', array($titre_role_u, 'u_lmdi_glossary', 'role')),
				
		);
	}
	
	public function revert_data()
	{
		// To get the $user array
		$user = $this->container->get('user');
		// Load the lang file
		$user->add_lang_ext('lmdi/gloss', 'gloss');
		$titre_role_a = $user->lang('ROLE_A_LMDI_GLOSSARY');
		$titre_role_u = $user->lang('ROLE_U_LMDI_GLOSSARY');
		// $descr_role_a = $user->lang('ROLE_A_LMDI_DESC');
		// $descr_role_u = $user->lang('ROLE_U_LMDI_DESC');
		
		return array(
			array('config.remove', array('lmdi_glossary')),
			array('config.remove', array('lmdi_glossary_ucp')),
			array('config.remove', array('lmdi_glossary_title')),
			array('config.remove', array('lmdi_glossary_usergroup')),
			array('config.remove', array('lmdi_glossary_admingroup')),
			
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
			array('permission.permission_unset', array($titre_role_a, 'a_lmdi_glossary')),
			array('permission.permission_unset', array($titre_role_u, 'u_lmdi_glossary')),
			
			// Role suppression
			array('permission.role_remove', array($titre_role_a)),
			array('permission.role_remove', array($titre_role_u)),
			
			// Remove permissions
			array('permission.remove', array('a_lmdi_glossary')),
			array('permission.remove', array('u_lmdi_glossary')),
			
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

	// group_creation in acp/gloss_module
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
