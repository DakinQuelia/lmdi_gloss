<?php
// helper.php
// (c) 2015-2016 - LMDI - Pierre Duhem
// Helper class

namespace lmdi\gloss\core;

class helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var string $table_prefix */
	protected $table_prefix;
	/** @var string */
	protected $glossary_table;
	/** @var string $phpbb_root_path */
	protected $phpbb_root_path;
	/** @var string phpEx */
	protected $php_ext;
	
	/**
	* Constructor
	*/
	public function __construct(\phpbb\db\driver\driver_interface $db, $table_prefix, $glossary_table, $phpbb_root_path, $php_ext)
	{
		$this->db 				= $db;
		$this->table_prefix 	= $table_prefix;
		$this->glossary_table 	= $glossary_table;
		$this->phpbb_root_path	= $phpbb_root_path;
		$this->php_ext			= $php_ext;
		
		const GLOSSARY_TABLE 	= $table_prefix . "glossary"; 
	}

	public function calcul_ilinks($ilinks)
	{
		$data = explode (",", $ilinks);
		$nb = count($data);
		$string = "";
		
		for ($i = 0; $i < $nb; $i++)
		{
			$terme = trim($data[$i]);
			$sql = "SELECT term_id FROM " . GLOSSARY_TABLE . " 
					WHERE term = " . $this->db->sql_escape($terme);
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$code = $row['term_id'];
			$this->db->sql_freeresult ($result);
			
			if ($code)
			{
				if (strlen ($string))
				{
					$string .= ", ";
				}
				$string .= "<a href=\"#$code\">" . $terme . "</a>";
			}
			else
			{
				if (strlen ($string))
				{
					$string .= ", ";
				}
				$string .= $terme;
			}
		}
		
		return ($string);
	}

	public function get_role_id($role_name)
	{
		$sql = 'SELECT role_id FROM ' . ACL_ROLES_TABLE . " 
				WHERE role_name = '" . $this->db->sql_escape($role_name) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow ($result);
		$role_id = (int) $row['role_id'];
		$this->db->sql_freeresult ($result);
		
		return $role_id;
	}
	
	public function get_group_id($group_name)
    {
        $sql = 'SELECT group_id FROM ' . GROUPS_TABLE . "
                WHERE group_name = '" . $this->db->sql_escape($group_name) . "'";
        $result = $this->db->sql_query($sql);
        $group_id = (int) $this->db->sql_fetchfield('group_id');
        $this->db->sql_freeresult($result); 

        return $group_id;
    } 

	public function group_deletion($group)
	{
		$group_id = $this->get_group_id($group);
		
		if ($group_id)
		{
			if (!function_exists('group_delete'))
			{
				include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
			}
			
			group_delete($group_id, $group);
		}
	}

	public function get_def_language($table, $colonne)
	{
		$sql = "SELECT DEFAULT($colonne) lg FROM (SELECT 1) AS dummy LEFT JOIN $table ON True";
		$result = $db->sql_query_limit($sql, 1); 
		$row = $this->db->sql_fetchrow ($result);
		$default = $row['lg'];
		$this->db->sql_freeresult ($result);
		
		return $default;
	}

	public function role_addition ($group, $role)
	{
		$group_id = $this->get_group_id($group);
		$role_id = $this->get_role_id($role);
		$sql = "INSERT into " . ACL_GROUPS_TABLE . " (group_id, forum_id, auth_option_id, auth_role_id, auth_setting)
				VALUES ($group_id, 0, 0, $role_id, 0)";
		$this->db->sql_query($sql);
	}

	public function role_deletion($group, $role)
	{
		$group_id = $this->get_group_id($group);
		$role_id = $this->get_role_id($role);
		$sql = "DELETE FROM " . ACL_GROUPS_TABLE . "
				WHERE group_id = " . (int) $group_id . " AND auth_role_id = " . (int) $role_id;
		$this->db->sql_query($sql);
	}

	public function group_creation($group, $desc)
	{
		$group_id = 0;
		$group_type = 0;
		$group_name = $group;
		$group_desc = $desc;

		$group_attributes = array(
			'group_colour' 			=> '000000',
			'group_rank' 			=> 0,
			'group_avatar' 			=> 0,
			'group_avatar_type' 	=> 0,
			'group_avatar_width'	=> 0,
			'group_avatar_height' 	=> 0,
			'group_legend' 			=> 0,
			'group_receive_pm' 		=> 0,
		);
			
		// Function in file includes/functions_user.php
		if (!function_exists('group_create') )
		{
			include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
		}
		
		group_create($group_id, $group_type, $group_name, $group_desc, $group_attributes);
		
		$group_id = $this->get_group_id($group_name);
        
        // Mark group hidden
        $sql = 'UPDATE ' . GROUPS_TABLE . ' SET group_type = ' . GROUP_HIDDEN . '
                WHERE group_id = ' . (int) $group_id;
        $this->db->sql_query($sql);
	}

	public function build_lang_select ()
	{
		$select = "";
		$lg = $this->get_def_language (GLOSSARY_TABLE, 'lang');
		$sql = 'SELECT lang_iso FROM ' . LANG_TABLE . ' ORDER BY lang_iso';
		$result = $this->db->sql_query($sql);
		
		while($row = $this->db->sql_fetchrow($result))
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
		
		$this->db->sql_freeresult($result);
		
		return ($select);
	}
}
