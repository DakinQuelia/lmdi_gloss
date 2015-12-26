<?php
/**
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\acp;

global $phpbb_root_path;
include ($phpbb_root_path . 'includes/functions_user.php');

class gloss_module {
	var $u_action;
	var $action;
	/** @var string */
	
	function get_def_language ($table, $colonne)
	{
		global $db;
		$sql = "SELECT DEFAULT($colonne) lg 
			FROM (SELECT 1) AS dummy
			LEFT JOIN $table ON True LIMIT 1";	
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow ($result);
		$default = $row['lg'];
		$db->sql_freeresult ($result);
		return ($default);
	}
	
	function role_addition ($group, $role)
	{
		global $table_prefix, $db;
		$group_id = $this->get_group_id ($group);
		$role_id = $this->get_role_id ($role);
		// ?? Vérifications préalables
		$sql = "INSERT into ${table_prefix}acl_groups 
			(group_id, forum_id, auth_option_id, auth_role_id, auth_setting)
			VALUES ($group_id, 0, 0, $role_id, 0)";
		// INSERT into phpbb3_acl_groups (group_id, forum_id, auth_option_id, auth_role_id, auth_setting) VALUES (4416, 0, 0, 52, 0)
		// var_dump ($sql);
		$db->sql_query($sql);
	}
	
	function role_deletion ($group, $role)
	{
		global $table_prefix, $db;
		$group_id = $this->get_group_id ($group);
		$role_id = $this->get_role_id ($role);
		// ?? Vérifications préalables
		$sql = "DELETE from ${table_prefix}acl_groups 
			WHERE group_id = '$group_id' AND auth_role_id = '$role_id'";
		// DELETE from phpbb3_acl_groups WHERE group_id = '4415' AND auth_role_id = '52'
		$db->sql_query($sql);
	}
	
	function group_creation($group, $desc)
	{
		$group_id = '';
		$group_type = 0;
		$group_name = $group;
		$group_desc = $desc;
		
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
		// File includes/functions_user.php
		$group = group_create($group_id, $group_type, $group_name, $group_desc, $group_attributes);
	}
	
	function get_role_id ($role_name)
	{
		global $table_prefix, $db;
		$sql = "SELECT role_id from ${table_prefix}acl_roles where role_name = '$role_name'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow ($result);
		$role_id = $row['role_id'];
		$db->sql_freeresult ($result);
		return ($role_id);
	}
	
	function get_group_id ($group_name)
	{
		global $table_prefix, $db;
		$sql = "SELECT group_id from ${table_prefix}groups where group_name = '$group_name'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow ($result);
		$group_id = $row['group_id'];
		$db->sql_freeresult ($result);
		return ($group_id);
	}
	
	function group_deletion ($group)
	{
		$group_id = $this->get_group_id ($group);
		if ($group_id) 
		{
			group_delete($group_id, $group);
		}
	}
	   
	function build_lang_select ()
	{
		global $table_prefix, $db, $user;
		
		$table = $table_prefix . 'glossary';
		$lg = $this->get_def_language ($table, 'lang');		
		$select  = "";
		
		$sql = 'SELECT lang_iso
			FROM ' . LANG_TABLE . '
			ORDER BY lang_iso';
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$lang = $row['lang_iso'];
			if ($lang == $lg)
			{
				$select .= "<option value=\"$lang\" selected>$lang</option>";
			}
			else
			{
				$select .= "<option value=\"$lang\">$lang</option>";
			}
		}
		$db->sql_freeresult($result);
		return ($select);
	}
	
	function main ($id, $mode) 
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $table_prefix, $phpbb_container;


		$user->add_lang_ext ('lmdi/gloss', 'gloss');

		$this->tpl_name = 'acp_gloss_body';
		$this->page_title = 'ACP_LEXICON';

		$action = request_var ('action', '');
		$action_config = $this->u_action . "&action=config";
	
		    
		switch ($action) {
			case 'config':
				// Update configuration
				$title = request_var('lmdi_gloss_title', '0');
				set_config ('lmdi_glossary_title', $title);
				$ucp = request_var('lmdi_gloss_ucp', '0');
				set_config ('lmdi_glossary_ucp', $ucp);
				$ucp = (int) $ucp;
				// Update UCP module display toggle
				$sql  = "UPDATE " . MODULES_TABLE;
				$sql .= " SET module_display = $ucp ";
				$sql .= "WHERE module_langname = 'UCP_GLOSS'";
				$db->sql_query($sql);
				// Update the lmdi_gloss column in table users
				$sql  = 'UPDATE ' . USERS_TABLE . " SET lmdi_gloss = $ucp ";
				$db->sql_query($sql);
				// Language selection
				$lang = request_var('lang', '');
				$table = $table_prefix . 'glossary';
				$lg = $this->get_def_language ($table, 'lang');
				if ($lang != $lg)
				{
					$sql = "ALTER TABLE ${table_prefix}glossary ALTER COLUMN lang SET DEFAULT '$lang'";
					$db->sql_query($sql);
				}
				// Usergroup creation/deletion
				$ug = request_var('lmdi_gloss_ugroup', '0');
				if ($config['lmdi_glossary_usergroup'] != $ug)
				{
					set_config ('lmdi_glossary_usergroup', $ug);
					$usergroup = $user->lang['GLOSSARY_EDITORS'];
					$userrole  = $user->lang['ROLE_U_LMDI_GLOSSARY'];
					$groupdesc   = $user->lang['ROLE_U_LMDI_DESC'];
					/*
					var_dump ($usergroup);
					var_dump ($userrole);
					var_dump ($groupdesc);
					*/
					if ($ug) 
					{
						$this->group_creation ($usergroup, $groupdesc);
						$this->role_addition ($usergroup, $userrole);
					}
					else 
					{
						$this->role_deletion ($usergroup, $userrole);
						$this->group_deletion ($usergroup);
					}
						
				}
				// Admin group creation/deletion
				$ag = request_var('lmdi_gloss_agroup', '0');
				if ($config['lmdi_glossary_admingroup'] != $ag)
				{
					set_config ('lmdi_glossary_admingroup', $ag);
					$admingroup = $user->lang['GLOSSARY_ADMINISTRATORS'];
					$adminrole  = $user->lang['ROLE_A_LMDI_GLOSSARY'];
					$groupdesc   = $user->lang['ROLE_A_LMDI_DESC'];
					/*
					var_dump ($admingroup);
					var_dump ($adminrole);
					var_dump ($groupdesc);
					*/
					if ($ag) 
					{
						$this->group_creation ($admingroup, $groupdesc);
						$this->role_addition ($admingroup, $adminrole);
					}
					else 
					{
						$this->role_deletion ($admingroup, $adminrole);
						$this->group_deletion ($admingroup);
					}
				}
				// Information message
				$message = $user->lang['CONFIG_UPDATED'];
				trigger_error($message . adm_back_link ($this->u_action));
				break;
			}	

		$select = $this->build_lang_select ();
		$template->assign_vars (array(
			'C_ACTION'      	=> $action_config,
			'ALLOW_FEATURE_NO' 	=> $config['lmdi_glossary_ucp'] == 0 ? 'checked="checked"' : '',
			'ALLOW_FEATURE_YES' => $config['lmdi_glossary_ucp'] == 1 ? 'checked="checked"' : '',
			'ALLOW_TITLE_NO' 	=> $config['lmdi_glossary_title'] == 0 ? 'checked="checked"' : '',
			'ALLOW_TITLE_YES'	=> $config['lmdi_glossary_title'] == 1 ? 'checked="checked"' : '',
			'CREATE_UGROUP_NO' 	=> $config['lmdi_glossary_usergroup'] == 0 ? 'checked="checked"' : '',
			'CREATE_UGROUP_YES'	=> $config['lmdi_glossary_usergroup'] == 1 ? 'checked="checked"' : '',
			'CREATE_AGROUP_NO' 	=> $config['lmdi_glossary_admingroup'] == 0 ? 'checked="checked"' : '',
			'CREATE_AGROUP_YES'	=> $config['lmdi_glossary_admingroup'] == 1 ? 'checked="checked"' : '',
			'S_LANG_OPTIONS'    => $select,
			));

	}	
	
} 	