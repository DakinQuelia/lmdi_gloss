<?php
/**
*
* @package phpBB Extension - LMDI extension de glossaire
* @copyright (c) 2015 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\migrations;

class release_1_0_0 extends \phpbb\db\migration\migration
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
			array('config.add', array('lmdi_glossary', 1)),
			array('config.add', array('lmdi_glossary_ucp', 0)),
			
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
	
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'lmdi_gloss' => array('BOOL', 0),
				),
			),
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
		);
	}

	public function revert_data()
	{
		return array(
			array('config.remove', array('lmdi_glossary')),
			array('config.remove', array('lmdi_glossary_ucp')),
			
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
