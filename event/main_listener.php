<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\event;

/**
* @ignore
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
* Event listener
*/
class main_listener implements EventSubscriberInterface
{
	/** @var \phpbb\cache\service */
	protected $cache;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\db\driver\driver_interface */
	protected $db;
	
	/* @var \phpbb\template\template */				
	protected $template;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\controller\helper */
	protected $helper;

	protected $glossary_table;

	public function __construct(
		\phpbb\db\driver\driver_interface $db,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		\phpbb\template\template $template,
		\phpbb\cache\service $cache,
		\phpbb\user $user,
		$glossary_table
		)
	{
		$this->db = $db;
		$this->config = $config;
		$this->helper = $helper;
		$this->template = $template;
		$this->cache = $cache;
		$this->user = $user;
		$this->glossary_table = $glossary_table;
	}

	static public function getSubscribedEvents ()
	{
	return array(
		'core.user_setup'					=> 'load_language_on_setup',
		'core.page_header'					=> 'build_url',
		// 'core.page_header'	=> 'add_page_header_link',
		'core.viewtopic_post_rowset_data'		=> 'insertion_glossaire',
		);
	}

	public function load_language_on_setup($event)
	{
		// Initial reset of the module_display row in the module table
		if (!$this->config['lmdi_glossary_ucp'])
		{
			$sql  = "UPDATE " . MODULES_TABLE;
			$sql .= " SET module_display = 0 ";
			$sql .= "WHERE module_langname = 'UCP_GLOSS'";
			$this->db->sql_query($sql);
		}
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'lmdi/gloss',
			'lang_set' => 'gloss',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function build_url ($event)
	{
		global $phpbb_root_path;
		global $phpEx;
		$this->template->assign_vars(array(
			'U_GLOSSAIRE'		=> $this->helper->route('lmdi_gloss_controller', array('mode' => 'glossaire')),
			'L_GLOSSAIRE'		=> $this->user->lang['LGLOSSAIRE'],
			'T_GLOSSAIRE'		=> $this->user->lang['TGLOSSAIRE'],
		));
	}

	// Event: core.viewtopic_post_rowset_data
	// Called for each post in the topic
	// event.rowset_data.post_text = text of the post
	public function insertion_glossaire($event)
	{
		if ($this->user->data['lmdi_gloss']) {
			$rowset_data = $event['rowset_data'];
			$post_text = $rowset_data['post_text'];
			$row = $event['row'];
			
			$post_text = $this->glossary_pass ($post_text);
			
			$rowset_data['post_text'] = $post_text;
			$event['rowset_data'] = $rowset_data;
		}
	}	

	/*
	*	Code replacing words found in the glossary table.
	*	Code de remplacement des éléments figurant dans la table de termes
	*/
	function glossary_pass ($texte)
	{
		static $glossterms;
		if (!isset ($glossterms) || !is_array ($glossterms))
			$glossterms = $this->compute_glossary_list();
		if (sizeof($glossterms)) {
			$rech = $glossterms['rech'];
			$remp = $glossterms['remp'];
			/*
			echo '<br><br>Chaînes de recherche :';
			var_dump($rech);
			echo '<br><br>Chaînes de remplacement :';
			var_dump($remp);
			*/
			// Sectionnement de la chaîne passée sur les éléments qui sont des balises.
			// Breaking the input string on delimiters (tags).
			preg_match_all ('#[][><][^][><]*|[^][><]+#', $texte, $parts);
			$parts = &$parts[0];
			// echo '<br><br>Sectionnement :';
			// var_dump($parts);
			if (empty($parts))
				return '';
			// Code to identify strings which we should not change.
			// Each time, a line to set and a line to reset the flag.
			// Code qui identifie les chaînes dans lesquelles il ne faut rien faire
			// À chaque fois, une ligne pour armer, une ligne pour désarmer
			foreach ($parts as $index => $part) {
				// Code
				if (strstr($part, '[code'))
					$code = true;
				if (!empty($code) && strstr($part, '[/code'))
					$code = false;
				// Images
				if (strstr($part, '[img'))
					$img = true;
				if (!empty($img) && strstr($part, '[/img'))
					$img = false;
				// Liens <a>
				if (strstr($part, '<a '))
					$link = true;
				if (!empty($link) && strstr($part, '</a'))
					$link = false;
				// Liens [url]
				if (strstr($part, '[url'))
					$link = true;
				if (!empty($link) && strstr($part, '[/url'))
					$link = false;
				// Script
				if (strstr($part, '<script '))
					$script = true;
				if (!empty($script) && strstr($part, '</script'))
					$script = false;
				if (!($part{0} == '<' && $parts[$index + 1]{0} == '>') &&
					!($part{0} == '[' && $parts[$index + 1]{0} == ']') &&
					empty($img) && empty($code) && empty($link) && empty($script) ) {
					// var_dump ($part);
					$part = preg_replace ($rech, $remp, $part);
					// var_dump ($part);
					$parts[$index] = $part;
					}
				}
			unset ($part);
			return implode ("", $parts);
			}
	}	// Fin de glossary_pass


	/*	Production of the term list and the replacement list, in an array named glossterms.
		The replacement string follows this model:
		<acronym class='id302' title=''>word</acronym>
		Production de la liste des termes et calcul d'une chaîne de remplacement.
		Les éléments sont placés dans le tableau glossterms. Ce tableau contient pour
		chaque rubrique un élément rech qui est la chaîne à rechercher et un
		élément remp qui est la chaîne de remplacement. Cette chaîne produit un
		balisage au modèle de :
		<acronym class='id302' title=''>rostre</acronym>
		*/
	function compute_glossary_list() 
	{
		$glossterms = $this->cache->get('_glossterms');
		if ($glossterms === false) 
		{
			$sql  = "SELECT * FROM $this->glossary_table ";
			// WHERE lang = '" . $user->data['user_lang'] . "'
			$sql .= "ORDER BY LENGTH(TRIM(variants)) DESC";
			$result = $this->db->sql_query($sql);
			$glossterms = array();
			while ($row = $this->db->sql_fetchrow($result)) 
			{
				$variants = explode (",", $row['variants']);
				// var_dump ($variants);
				$cnt = count ($variants);
				for ($i = 0 ; $i < $cnt ; $i++) 
				{
					$variant = trim ($variants[$i]);
					$remp  = "<acronym class='id$row[term_id]' title=''>";
					// $remp .= preg_quote(trim($row['description']));
					// $remp .= "'>";
					$remp .= $variant;
					$remp .= "</acronym>";
					$firstspace = '/\b(';
					$lastspace = ')\b/u';	// u for UTF-8
					$rech = $firstspace . $variant . $lastspace;
					// var_dump ($rech); echo ("<br>\n");
					$glossterms['rech'][] = $rech;
					// var_dump ($remp); echo ("<br>\n");
					$glossterms['remp'][] = $remp;
				}
			}
			$this->db->sql_freeresult($result);
			$this->cache->put('_glossterms', $glossterms, 86400);		// 24 h
			
		}
		return $glossterms;
	}	// Fin de compute_acronym_list

}	

