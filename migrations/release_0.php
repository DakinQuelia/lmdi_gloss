<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015-2016 Pierre Duhem - LMDI
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\migrations;

use \phpbb\db\migration\container_aware_migration;

global $phpbb_root_path;
include ($phpbb_root_path . 'includes/functions_user.php');

class release_0 extends container_aware_migration
{
	
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
						'lang'	=> array ('VCHAR:2', 'en'),
					),
				'PRIMARY_KEY'   => 'term_id',
				),
			),
		);
	}
	
	function get_nbrows ($table)
	{
		$sql = "SELECT COUNT(*) as nb FROM $table WHERE 1";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$nb = $row['nb'];
		return ((int)$nb);
	}
	
    	public function revert_schema()
	{
		$nbrows = $this->get_nbrows($this->table_prefix . 'glossary');
		if ($nbrows < 5)
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
		else 
		{
			return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'users'			=> array(
					'lmdi_gloss',
				),
			),
			);
		}
	}

	
}
