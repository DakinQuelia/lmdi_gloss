<?php
/**
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\acp;

class gloss_module {
var $u_action;
var $action;

/** @var string */
protected $lexicon_table;

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
				$ucp = request_var('lmdi_gloss_ucp', '0');
				set_config ('lmdi_glossary_ucp', $ucp);
				$ucp = (int) $ucp;
				// Update UCP module display toggle
				$sql  = "UPDATE " . MODULES_TABLE;
				$sql .= " SET module_display = $ucp ";
				$sql .= "WHERE module_langname = 'UCP_GLOSS'";
				$db->sql_query($sql);
				// Update the lmdi_gloss column in table users
				$sql  = "UPDATE " . USERS_TABLE;
				$sql .= " SET lmdi_gloss = $ucp ";
				$db->sql_query($sql);
				// Information message
				$message = $user->lang['CONFIG_UPDATED'];
				trigger_error($message . adm_back_link($this->u_action));
				break;
			}	

		$template->assign_vars (array(
			'C_ACTION'      	=> $action_config,
			'ALLOW_FEATURE_NO' 	=> $config['lmdi_glossary_ucp'] == 0 ? 'checked="checked"' : '',
			'ALLOW_FEATURE_YES' => $config['lmdi_glossary_ucp'] == 1 ? 'checked="checked"' : '',
			));

	}	
	
} 	