<?php
/**
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015-2016 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\acp;

class gloss_module {

	protected $gloss_helper;
	var $u_action;
	var $action;

	public function main ($id, $mode)
	{
		global $db, $user, $auth, $template, $cache, $request;
		global $config, $phpbb_root_path, $phpbb_admin_path, $phpEx;
		global $table_prefix, $phpbb_container;

		$user->add_lang_ext ('lmdi/gloss', 'gloss');

		$this->tpl_name = 'acp_gloss_body';
		$this->page_title = $user->lang('ACP_GLOSS_TITLE');

		$this->gloss_helper = $phpbb_container->get('lmdi.gloss.core.helper');

		$action = $request->variable ('action', '');
		$action_config = $this->u_action . "&action=config";

		// var_dump ($action);
		if ($action == 'config') 
		{
			if (!check_form_key('acp_gloss_body'))
			{
				trigger_error('FORM_INVALID');
			}
			else
			{
			// Update configuration
			$title = $request->variable('lmdi_gloss_title', '0');
			$config->set ('lmdi_glossary_title', $title);
			$ucp = $request->variable('lmdi_gloss_ucp', '0');
			$config->set ('lmdi_glossary_ucp', $ucp);
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
			$lang = $request->variable('lang', '');
			$table = $table_prefix . 'glossary';
			$lg = $this->gloss_helper->get_def_language ($table, 'lang');
			if ($lang != $lg)
			{
				$sql = "ALTER TABLE ${table_prefix}glossary ALTER COLUMN lang SET DEFAULT '$lang'";
				$db->sql_query($sql);
			}
			// Pixel limit
			$px = $request->variable('pixels', '400');
			$config->set ('lmdi_glossary_pixels', $px);
			// Picture weight
			$ko = $request->variable('poids', '200');
			$config->set ('lmdi_glossary_poids', $ko);
			// Usergroup creation/deletion
			$ug = $request->variable('lmdi_gloss_ugroup', '0');
			if ($config['lmdi_glossary_usergroup'] != $ug)
			{
				$config->set ('lmdi_glossary_usergroup', $ug);
				$usergroup = $user->lang('GROUP_GLOSS_EDITOR');
				// $userrole  = $user->lang('ROLE_GLOSS_EDITOR');
				$groupdesc = $user->lang('GROUP_DESCRIPTION_GLOSS_EDITOR');
				// $usergroup = 'GROUP_GLOSS_EDITOR';
				$userrole  = 'ROLE_GLOSS_EDITOR';
				// $groupdesc = 'GROUP_DESCRIPTION_GLOSS_EDITOR';
				if ($ug)
				{
					$this->gloss_helper->group_creation ($usergroup, $groupdesc);
					$this->gloss_helper->role_addition ($usergroup, $userrole);
				}
				else
				{
					$this->gloss_helper->role_deletion ($usergroup, $userrole);
					$this->gloss_helper->group_deletion ($usergroup);
				}
			}
			// Admin group creation/deletion
			$ag = $request->variable('lmdi_gloss_agroup', '0');
			if ($config['lmdi_glossary_admingroup'] != $ag)
			{
				$config->set ('lmdi_glossary_admingroup', $ag);
				$admingroup = $user->lang('GROUP_GLOSS_ADMIN');
				// $adminrole  = $user->lang('ROLE_GLOSS_ADMIN');
				$groupdesc  = $user->lang('GROUP_DESCRIPTION_GLOSS_ADMIN');
				// $admingroup = 'GROUP_GLOSS_ADMIN';
				$adminrole  = 'ROLE_GLOSS_ADMIN';
				// $groupdesc  = 'GROUP_DESCRIPTION_GLOSS_ADMIN';
				if ($ag)
				{
					$this->gloss_helper->group_creation ($admingroup, $groupdesc);
					$this->gloss_helper->role_addition ($admingroup, $adminrole);
				}
				else
				{
					$this->gloss_helper->role_deletion ($admingroup, $adminrole);
					$this->gloss_helper->group_deletion ($admingroup);
				}
			}
			// Information message
			$message = $user->lang['CONFIG_UPDATED'];
			trigger_error($message . adm_back_link ($this->u_action));
			}
		}

		$form_key = 'acp_gloss_body';
		add_form_key ($form_key);
		$select = $this->gloss_helper->build_lang_select ();
		$pixels = $config['lmdi_glossary_pixels'];
		if (!$pixels)
		{
			$pixels = 500;
		}
		$poids  = $config['lmdi_glossary_poids'];
		if (!$poids)
		{
			$poids = 150;
		}
		$template->assign_vars (array(
			'C_ACTION'		=> $action_config,
			'ALLOW_FEATURE_NO'	=> $config['lmdi_glossary_ucp'] == 0 ? 'checked="checked"' : '',
			'ALLOW_FEATURE_YES'	=> $config['lmdi_glossary_ucp'] == 1 ? 'checked="checked"' : '',
			'ALLOW_TITLE_NO'	=> $config['lmdi_glossary_title'] == 0 ? 'checked="checked"' : '',
			'ALLOW_TITLE_YES'	=> $config['lmdi_glossary_title'] == 1 ? 'checked="checked"' : '',
			'CREATE_UGROUP_NO'	=> $config['lmdi_glossary_usergroup'] == 0 ? 'checked="checked"' : '',
			'CREATE_UGROUP_YES'	=> $config['lmdi_glossary_usergroup'] == 1 ? 'checked="checked"' : '',
			'CREATE_AGROUP_NO'	=> $config['lmdi_glossary_admingroup'] == 0 ? 'checked="checked"' : '',
			'CREATE_AGROUP_YES'	=> $config['lmdi_glossary_admingroup'] == 1 ? 'checked="checked"' : '',
			'S_PIXELS'		=> $pixels,
			'S_POIDS'			=> $poids,
			'S_LANG_OPTIONS'    => $select,
			));

	}

}
