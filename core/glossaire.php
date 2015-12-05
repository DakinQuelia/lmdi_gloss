<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace lmdi\gloss\core;

class glossaire
{
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var string */
	protected $phpEx;
	/** @var string phpBB root path */
	protected $phpbb_root_path;
	/** @var string */
	protected $glossary_table;
	/** @var \phpbb\extension\manager "Extension Manager" */
	protected $ext_manager;
	/** @var \phpbb\path_helper */
	protected $path_helper;
	// Strings
	protected $ext_path;
	protected $ext_path_web;

	/**
	* Constructor
	*
	* @param \phpbb\template\template		 	$template
	* @param \phpbb\user						$user
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\controller\helper		 	$helper
	* @param \phpbb\config\config				$config
	* @param									$phpEx
	* @param									$phpbb_root_path
	* @param string 							$glossary_table
	*
	*/
	public function __construct(
		\phpbb\template\template $template, 
		\phpbb\user $user, 
		\phpbb\db\driver\driver_interface $db, 
		\phpbb\controller\helper $helper, 
		\phpbb\auth\auth $auth, 
		\phpbb\extension\manager $ext_manager,
		\phpbb\path_helper $path_helper,
		$phpEx, 
		$phpbb_root_path, 
		$glossary_table)
	{
		$this->template 		= $template;
		$this->user 			= $user;
		$this->db 			= $db;
		$this->helper 			= $helper;
		$this->auth			= $auth;
		$this->phpEx 			= $phpEx;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->glossary_table 	= $glossary_table;
		$this->ext_manager	 	= $ext_manager;
		$this->path_helper	 	= $path_helper;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/gloss', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}
	
	var $u_action;

	function main()
	{
		// SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a FROM phpbb3_glossary ORDER BY a
		$sql  = 'SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a
				FROM ' . $this->glossary_table . '
				ORDER BY a';
		$result = $this->db->sql_query($sql);

		$abc_links = '<span id="haut"></span><br /><p class="glossa">';

		$str_terme = $this->user->lang['GLOSS_ED_TERM'];
		$str_defin = $this->user->lang['GLOSS_ED_DEF'];
		$str_illus = $this->user->lang['GLOSS_ED_PICT'];
		$str_action = $this->user->lang['GLOSS_DISPLAY'];

		$corps  = '<table class="deg"><tr class="deg">';
		$corps .= '<th class="deg0">' . $str_terme . '</th>';
		$corps .= '<th class="deg0">' . $str_defin . '</th>';
		$corps .= '<th class="deg1">' . $str_illus . '</th></tr>';

		$cpt  = 0;
		$top = $this->ext_path_web . "/styles/top.gif";
		while ($row = $this->db->sql_fetchrow($result))
		{
			$l = $row['a'];
			$abc_links .= "<a href =\"#$l\">$l</a> " ;

			$sql  = 'SELECT * 
					FROM ' . $this->glossary_table . "
					WHERE LEFT($this->glossary_table.term, 1) = '$l' 
					ORDER BY term";
			$result2 = $this->db->sql_query ($sql);

			$cpt++;
			$corps .= "<tr class=\"deg\"><td class=\"glossi\" colspan=\"2\" id=$l>&nbsp;$l</td>";
			$corps .= "<td class=\"haut\"><a href=\"#haut\"><img src=\"$top\"></a></td></tr>";
			while ($arow = $this->db->sql_fetchrow($result2)) {
				$code = $arow['term_id'];
				$vari = $arow['variants'];
				$term = $arow['term'];
				$desc = $arow['description'];
				$pict = $arow['picture'];
				$corps .= '<tr class="deg">';
				$corps .= '<td class="deg0"><b>' . $term . '</b></td>';
				$corps .= '<td class="deg0">' . $desc . '</td>';
				$corps .= '<td class="deg1">';
				/*	Nous ne mettons un lien cliquable que si l'image est différente de nopict.
					Link only if the picture is not nopict.
					*/
				  if ($pict != "nopict") {
					$url = $this->helper->route('lmdi_gloss_controller', array('mode' => 'glosspict', 'code' => $code, 'term' =>$term, 'pict' => $pict));
					   $corps .= '<a href="' . $url . '">' . $str_action . '</a></td>';
					   }
				  else {
					   $corps .= "&nbsp;</td>";
					   }
				$corps .= "</tr>";
				}	// Fin du while sur le contenu
			$this->db->sql_freeresult ($result2);
			}	// Fin du while sur les initiales
		$this->db->sql_freeresult ($result);
		// Fermeture de la table
		$corps .= "</table>";
		// Fermeture de la ligne de liens avec un lien vers la page d'administration
		$str_admin = $this->user->lang['GLOSS_EDITION'];
		if ($this->auth->acl_get('a_')) {
			$abc_links .= ' -- <a href="';
			$abc_links .= append_sid("{$this->phpbb_root_path}app.php/gloss?mode=glossedit");
			// $abc_links .= append_sid("{$this->phpbb_root_path}ext/lmdi/gloss/glossedit.{$this->phpEx}");
			$abc_links .= '">' . $str_admin . '</a>';
			}
		$abc_links .= "</p><br />";

		// Bibliographie 
		$biblio = "
		<p class=\"m\">
		<u>Bibliographie</u><br /> 
		<br />Aguilar d' (J.), Glossaire entomologique, Delachaux et Niestlé, Paris, 2004
		<br />Berland (L.), Faune de France n° 10 - Hyménoptères vespiformes. vol. I., 1925
		<br />Chinery (M.), Insectes de France et d'Europe occidentale, Flammarion, Paris, 2005
		<br />Chatenet du  (G.), Guide des coléoptères d'Europe, Delachaux et Niestlé, Paris, 1986
		<br />Haupt (J. et H.), Guide des mouches et des moustiques, Delachaux et Niestlé, Paris, 2000
		<br />Paulian (R.), Biologie des coléoptères, Editions Lechevalier, Paris, 1988
		<br />Roth (M.), Initiation à la morphologie, la systématique et la biologie des Insectes, Initiations et documentations techniques, n° 23, Orstom, Paris, 1980
		<br />Séguy (E.), Faune de France n° 6 - Diptères Anthomyides, 1923
		<br />Séguy (E.), Dictionnaire des termes d'Entomologie, éditions Paul Lechevalier, Paris, 1967
		<br />Tolman (T.), Lewintgton (R.), Guide des papillons d'Europe et d'Afrique du Nord Delachaux et Niestlé, Paris, 2007
		<br />Villemant (C.), Blanchot (P.), Portraits d'insectes, Seuil 2004
		<br /><br /> 
		<u>Webographie</u><br /><br />
		<a href=\"http://www.galerie-insecte.org/galerie/fichier.php\">Galerie-insecte</a> <br />
		<a href=\"http://www7.inra.fr/hyppz/glossair.htm\">INRA, Le glossaire de HYPPZ</a><br />  
		<a href=\"http://www.libellules.org/fra/fra_index.php\">SFO Société Française d'Odonatologie, glossaire odonatologique</a><br />
		<a href=\"http://www.ird.fr/\">IRD, institut de recherche pour le développement </a><br />
		<br />
		<u>Illustrations</u><br /><br />
		<a href=\"http://www.faunedefrance.org\">Faune de France</a> <br /> <br /> 
		<u>Photographes et illustrateurs</u> <br /><br />
		<a href=\"http://www.galerie-insecte.org/galerie/auteur.php?aut=107\">Alastor</a>,
		<a href=\"http://www.galerie-insecte.org/galerie/auteur.php?aut=1574\">Fred Chevaillot</a>,
		<a href=\"http://www.galerie-insecte.org/galerie/auteur.php?aut=5\">bobgaia</a>,
		<a href=\"http://www.galerie-insecte.org/galerie/auteur.php?aut=2408\">lweit</a> 
		<br /> 
		</p>";

		// Information sur l'existence d'une illustration
		$illustration = $this->user->lang['ILLUSTRATION'];

		page_header('Glossaire entomologique');
		$this->template->set_filenames (array(
			'body' => 'gloss/glossaire.html',
		));
		$this->template->assign_vars (array (
			'U_TITRE'			=> $this->user->lang['TGLOSSAIRE'],
			'U_ABC'			=> $abc_links,
			'U_ILLUST'		=> $illustration,
			'U_CORPS'			=> $corps,
			'U_BIBLIO'		=> $biblio,
		));

		page_footer();
	}
}

	