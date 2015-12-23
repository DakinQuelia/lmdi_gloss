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
	protected $lexicon_table;

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
	
	function group_deletion($group)
	{
		global $table_prefix, $db;
		$sql = "SELECT group_id from ${table_prefix}groups where group_name = '$group'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow ($result);
		$group_id = $row['group_id'];
		$db->sql_freeresult ($result);
		if ($group_id) 
		{
			group_delete($group_id, $group);
		}
	}

   
	/**
	* Update group-specific ACL options. Function can grant or remove options. If option already granted it will NOT be updated.
	* Found: https://www.phpbb.com/support/docs/en/3.1/kb/article/permission-system-overview-for-mod-authors-part-two/
	*
	* @param grant|remove $mode defines whether roles are granted to removed
	* @param string $group_name group name to update
	* @param mixed $options auth_options to grant (a auth_option has to be specified)
	* @param ACL_YES|ACL_NO|ACL_NEVER $auth_setting defines the mode acl_options are getting set with
	*
	*/
	function update_group_permissions($mode='grant', $group_name, $options=array(), $auth_setting = ACL_YES)
	{
		global $db, $auth, $cache;

		// First We Get Group ID
		$sql = "SELECT g.group_id
			FROM " . GROUPS_TABLE . " g
			WHERE group_name = '$group_name'";
		$result = $db->sql_query($sql);
		$group_id = (int) $db->sql_fetchfield('group_id');
		$db->sql_freeresult($result);
		
		// Now Lets Get All Current Options For Group
		$group_options = array();
		$sql = "SELECT auth_option_id
			FROM " . ACL_GROUPS_TABLE . "
			WHERE group_id = " . (int) $group_id . "
			GROUP BY auth_option_id";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$group_options[] = $row;
		}
		$db->sql_freeresult($result);
		
		// Get Option ID Values For Options Granting Or Removing
		$acl_options_ids = array();
		$sql = "SELECT auth_option_id
				FROM " . ACL_OPTIONS_TABLE . "
				WHERE " . $db->sql_in_set('auth_option', $options) . "
				GROUP BY auth_option_id";
		$result = $db->sql_query($sql);
		while ($row = $db->sql_fetchrow($result))
		{
			$acl_options_ids[] = $row;
		}
		$db->sql_freeresult($result);
		

		// If Granting Permissions
		if ($mode == 'grant')
		{
			// Make Sure We Have Option IDs
			if (empty($acl_options_ids))
			{
				return false;
			}
		 
			// Build SQL Array For Query
			$sql_ary = array();
			for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
			{
				//If Option Already Granted To Role Then Skip It
				if (in_array($acl_options_ids[$i]['auth_option_id'], $group_options))
				{
				    continue;
				}
				$sql_ary[] = array(
				    'group_id'        => (int) $group_id,
				    'auth_option_id'    => (int) $acl_options_ids[$i]['auth_option_id'],
				    'auth_setting'        => $auth_setting,
				);
			}

			$db->sql_multi_insert(ACL_GROUPS_TABLE, $sql_ary);
			$cache->destroy('acl_options');
			$auth->acl_clear_prefetch();
		}

		// If Removing Permissions
		if ($mode == 'remove')
		{
			//Make Sure We Have Option IDs
			if (empty($acl_options_ids))
			{
				return false;
			}
			 
			// Process Each Option To Remove
			for ($i = 0, $count = sizeof($acl_options_ids);$i < $count; $i++)
			{
				$sql = "DELETE
					FROM " . ACL_GROUPS_TABLE . "
					WHERE auth_option_id = " . $acl_options_ids[$i]['auth_option_id'];
				$db->sql_query($sql);
			}

		$cache->destroy('acl_options');
		$auth->acl_clear_prefetch();
		}

		return;
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
				// Usergroup creation/deletion
				$ug = request_var('lmdi_gloss_ugroup', '0');
				if ($config['lmdi_glossary_usergroup'] != $ug)
				{
					set_config ('lmdi_glossary_usergroup', $ug);
					$usergroup = $user->lang['GLOSSARY_EDITORS'];
					$userrole  = $user->lang['ROLE_U_LMDI_GLOSSARY'];
					$groupdesc   = $user->lang['ROLE_U_LMDI_DESC'];
					if ($ug) 
					{
						$this->group_creation ($usergroup, $groupdesc);
						$this->update_group_permissions ('grant', $usergroup, array($userrole));
					}
					else 
					{
						$this->group_deletion ($usergroup);
						$this->update_group_permissions ('remove', $usergroup, array($userrole));
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
					if ($ag) 
					{
						$this->group_creation ($admingroup, $groupdesc);
						$this->update_group_permissions ('grant', $admingroup, array($adminrole));
					}
					else 
					{
						$this->group_deletion ($admingroup);
						$this->update_group_permissions ('remove', $admingroup, array($adminrole));
					}
				}
				// Information message
				$message = $user->lang['CONFIG_UPDATED'];
				trigger_error($message . adm_back_link ($this->u_action));
				break;
			}	

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
			));

	}	
	
} 	