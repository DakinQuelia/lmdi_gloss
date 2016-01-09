<?php
// glossedit.php
// (c) Pierre Duhem - LMDI - 2015
// Version de glossaire.php utilisée pour les administrateurs
// Edition page for administrators

namespace lmdi\gloss\core;

class glossedit
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
	/** @var \phpbb\cache\service */
	protected $cache;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\files\factory */
	// protected $files_factory;
	// Strings
	protected $ext_path;
	protected $ext_path_web;

	/**
	* Constructor
	*
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
		\phpbb\cache\service $cache,
		\phpbb\config\config $config,
		// \phpbb\files\factory $files_factory,
		$phpEx, 
		$phpbb_root_path, 
		$glossary_table)
	{
		$this->template 		= $template;
		$this->user 			= $user;
		$this->db 			= $db;
		$this->helper 			= $helper;
		$this->auth			= $auth;
		$this->ext_manager	 	= $ext_manager;
		$this->path_helper	 	= $path_helper;
		$this->cache             = $cache;
		$this->config            = $config;
		// $this->files_factory 	= $files_factory;
		$this->phpEx 			= $phpEx;
		$this->phpbb_root_path 	= $phpbb_root_path;
		$this->glossary_table 	= $glossary_table;

		$this->ext_path = $this->ext_manager->get_extension_path('lmdi/gloss', true);
		$this->ext_path_web = $this->path_helper->update_web_root_path($this->ext_path);
	}
	
	var $u_action;

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

	function main()
	{
	global $table_prefix, $phpbb_root_path, $phpEx, $request;
	
	$this->user->add_lang_ext('lmdi/gloss', 'gloss');
	
	$abc_links = "";
	$illustration = "";
	$corps = "";
	$biblio = "";
	$table = $table_prefix . "glossary";

	$num    = $request->variable ('code', 0);
	$action = $request->variable ('action', "rien");
	$delete = $request->variable ('delete', "rien");
	$save   = $request->variable ('save', "rien");
	if ($delete != 'rien')
		$action = 'delete';
	if ($save != 'rien')
		$action = 'save';

	$str_colon = $this->user->lang['COLON'];

	// var_dump ($action);
	
	switch ($action) {
		case "edit" :
			if ($num < 0) {	// Création d'une fiche
				$code = "";
				$vari = "";
				$term = "";
				$desc = "";
				$pict = "";
				$lang = $this->get_def_language ($table, 'lang');
				$str_action = $this->user->lang['GLOSS_CREAT'];
				}
			else {			// Édition d'une fiche
				$sql  = "SELECT * ";
				$sql .= "FROM $table ";
				$sql .= "WHERE term_id = \"$num\" ";
				// echo ("Valeur de la requête : $sql.<br>\n");
				$result = $this->db->sql_query ($sql);
				$row = $this->db->sql_fetchrow ($result);
				$code = $row['term_id'];
				$vari = $row['variants'];
				$term = $row['term'];
				$desc = $row['description'];
				$pict = $row['picture'];
				$lang = $row['lang'];
				$this->db->sql_freeresult ($result);
				$str_action = $this->user->lang['GLOSS_EDIT'];
				}
			$str_variants = $this->user->lang['GLOSS_VARIANTS'] . $str_colon;
			$str_terme = $this->user->lang['GLOSS_TERM'] . $str_colon;
			$str_varex = $this->user->lang['GLOSS_VARIANTS_EX'];
			$str_terex = $this->user->lang['GLOSS_TERM_EX'];
			$str_desc  = $this->user->lang['GLOSS_DESC'] . $str_colon;
			$str_pict  = $this->user->lang['GLOSS_PICT'] . $str_colon;
			$str_pictex= $this->user->lang['GLOSS_ED_PIEXPL'];
			$str_upload= $this->user->lang['UPLOAD_FILE'];
			$str_lang  = $this->user->lang['GLOSS_LANG'] . $str_colon;
			$str_regis = $this->user->lang['GLOSS_REGIS'];
			$str_suppr = $this->user->lang['GLOSS_SUPPR'];
			$form  = "<form action=\"";
			$form .= append_sid ($phpbb_root_path."app.php/gloss?mode=glossedit");
			$form .= "\" method=\"post\" id=\"glossedit\" enctype=\"multipart/form-data\">";
			$form .= "<div class=\"panel\"><div class=\"inner\"><div class=\"content\">";
			$form .= "<h2 class=\"login-title\">$str_action</h2>";
			// Deux lignes cachées pour le numéro et la langue
			$form .= "<input type=\"hidden\" name=\"term_id\" id=\"term_id\" value=\"$code\">";
			$form .= "<input type=\"hidden\" name=\"lang\" id=\"lang\" value=\"$lang\">";
			$form .= "<fieldset class=\"fields1\">";
			$form .= "<dl>";
			$form .= "<dt><label for=\"vari\">$str_variants</label><br />";
			$form .= "<span>$str_varex</span></dt>";
			$form .= "<dd><input type=\"text\" tabindex=\"1\" name=\"vari\" ";
			$form .= "id=\"term\" size=\"25\" value=\"$vari\" class=\"inputbox autowidth\" /></dd>";
			$form .= "</dl>";
			$form .= "<dl>";
			$form .= "<dt><label for=\"term\">$str_terme</label><br />";
			$form .= "<span>$str_terex</span></dt>";
			$form .= "<dd><input type=\"text\" tabindex=\"2\" name=\"term\" ";
			$form .= "id=\"term\" size=\"25\" value=\"$term\" class=\"inputbox autowidth\" /></dd>";
			$form .= "</dl>";
			$form .= "<dl>";
			$form .= "<dt><label for=\"desc\">$str_desc</label></dt>";
			$form .= "<dd><textarea tabindex=\"3\" rows=\"4\" cols=\"50\" name=\"desc\">$desc</textarea>";
			$form .= "</dd>";
			$form .= "</dl>";
			$form .= "<dl>";
			$form .= "<dt><label for=\"lang\">$str_lang</label></dt>";
			$form .= "<dd><input type=\"text\" tabindex=\"5\" name=\"lang\" ";
			$form .= "id=\"lang\" size=\"25\" value=\"$lang\" class=\"inputbox autowidth\" /></dd>";
			$form .= "</dl>";
			if ($pict == "" || $pict == "nopict")
			{
				$form .= "<dl>";
				$form .= "<dt><label for=\"upload_file\">$str_pict</label><br />";
				$form .= "<span>$str_upload</span></dt>";
				$form .= "<input type=\"file\" name=\"upload_file\" id=\"upload_file\" class=\"inputbox autowidth\" /></dd>";
				$form .= "</dl>";
			}
			else
			{
				$form .= "<dl>";
				$form .= "<dt><label for=\"pict\">$str_pict</label><br />";
				$form .= "<span>$str_pictex</span></dt>";
				$form .= "<dd><input type=\"text\" tabindex=\"4\" name=\"pict\" ";
				$form .= "id=\"pict\" size=\"25\" value=\"$pict\" class=\"inputbox autowidth\" /></dd>";
				$form .= "</dl>";
			}
			$form .= "<dl>";
			$form .= "<dt>&nbsp;</dt>";
			$form .= "<dd><input type=\"submit\" name=\"save\" id=\"save\" tabindex=\"5\" value=\"$str_regis\" class=\"button1\" />&nbsp;&nbsp;";
			$form .= "<input type=\"submit\" name=\"delete\" id=\"delete\" tabindex=\"6\" value=\"$str_suppr\" class=\"button1\" /></dd>";
			$form .= "</dl>";
			$form .= "</fieldset>";
			$form .= "</div></div></div>";
			$abc_links = $form;
			break;
		case "save" :	
			$term_id     = $this->db->sql_escape ($request->variable ('term_id', 0));
			$term        = $this->db->sql_escape ($request->variable ('term', "", true));
			$variants    = $this->db->sql_escape ($request->variable ('vari', "", true));
			$description = $this->db->sql_escape ($request->variable ('desc', "", true));
			$lang        = $this->db->sql_escape ($request->variable ('lang', "fr", true));
			/*
			// Which version are we using?
			if (version_compare ( '3.2.0', $this->config['version'], '>='))
			{
				$picture = $this->upload_32x ();
			}
			else 
			{
				$picture = $this->upload_31x ();
			}
			*/
			$picture = $this->upload_31x ();
			$picture = substr($picture, 0, strpos($picture, "."));
			$picture = $this->db->sql_escape ($picture);
			if ($term_id == 0) 
			{
				$sql  = "INSERT INTO $table ";
				$sql .= "(variants, term, description, picture, lang) ";
				$sql .= " VALUES ";
				$sql .= "(\"$variants\", \"$term\", \"$description\", \"$picture\", \"$lang\")";
				$this->db->sql_query ($sql);	
				$term_id = $this->db->sql_nextid();
			}
			else 
			{
				$sql  = "UPDATE $table SET ";
				$sql .= "term_id       = \"$term_id\", ";
				$sql .= "variants      = \"$variants\", ";
				$sql .= "term          = \"$term\", ";
				$sql .= "description   = \"$description\", ";
				$sql .= "picture       = \"$picture\", ";
				$sql .= "lang          = \"$lang\" ";
				$sql .= "WHERE term_id = \"$term_id\" ";
				$sql .= "LIMIT 1";
				$this->db->sql_query ($sql);	
			}	
			// Purge the cache
			$this->cache->destroy('_glossterms');	
			// Redirection
			$params = "mode=glossedit&code=$term_id";	
			$url  = append_sid ($phpbb_root_path."app.php/gloss", $params);
			$url .= "#$term_id";	// Anchor target term_id
			redirect ($url);
			break;
		case "delete" :
			$term_id     = $this->db->sql_escape ($request->variable ('term_id', 0));
			$sql  = "DELETE ";
			$sql .= "FROM $table ";
			$sql .= "WHERE term_id = \"$term_id\" ";
			$sql .= "LIMIT 1";
			// echo ("Valeur de la requête : $sql.<br>\n");
			$this->db->sql_query ($sql);	
			// Purge the cache
			$this->cache->destroy('_glossterms');	
			// Redirection
			$cap = substr ($request->variable ('term', "", true), 0, 1);
			$params = "mode=glossedit";
			$url  = append_sid ($phpbb_root_path."app.php/gloss", $params);
			$url .= "#$cap";		// Anchor target = initial cap 
			redirect ($url);
			// header("Location:$url");
			break;
		case "rien" :
			$sql  = "SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a ";
			$sql .= "FROM $table ";
			// $sql .= " WHERE lang = '" . $this->user->lang['USER_LANG'] . "'";
			$sql .= " ORDER BY a" ;
			// echo ("Valeur de la requête : $sql.<br>\n");
			$result = $this->db->sql_query ($sql);

			$str_titre = $this->user->lang['GLOSS_EDITION'];
			$str_terme = $this->user->lang['GLOSS_ED_TERM'];
			$str_defin = $this->user->lang['GLOSS_ED_DEF'];
			$str_illus = $this->user->lang['GLOSS_ED_PICT'];
			$str_action = $this->user->lang['GLOSS_ED_ACT'];
			$abc_links = "<span id=\"haut\"></span>\n";
			$abc_links .= "<h2 class=\"login-title\">$str_titre</h2>";
			$abc_links .= "<p class=\"glossa\">";
			
			$corps  = "<table class=\"deg\"><tr class=\"deg\">";
			$corps .= "<th class=\"deg0\">$str_terme</th>";
			$corps .= "<th class=\"deg0\">$str_defin</th>";
			$corps .= "<th class=\"deg1\">$str_illus</th>";
			$corps .= "<th class=\"deg1\">$str_action</th></tr>";
			
			$cpt  = 0;
			$str_edit  = $this->user->lang['GLOSS_ED_EDIT'];
			$top = $this->ext_path_web . "/styles/top.gif";
			while ($row = $this->db->sql_fetchrow ($result)) {
				// print_r ($row);
				$l = $row['a'];
				$abc_links .= "<a href =\"#$l\">$l</a> " ;

				$sql  = "SELECT * ";
				$sql .= "FROM $table ";
				$sql .= "WHERE LEFT($table.term, 1) = \"$l\" ";
				// $sql .= "WHERE lang = '" . $this->user->lang['USER_LANG'] . "' ";
				$sql .= "ORDER BY term";
				// echo ("Valeur de la requête : $sql.<br>\n");
				$result2 = $this->db->sql_query ($sql);

				$cpt++;
				$corps .= "\n<tr class=\"deg\"><td class=\"glossi\" colspan=\"3\" id=\"$l\">&nbsp;$l</td>";
				$corps .= "<td class=\"haut\"><a href=\"#haut\"><img src=\"$top\"></a></td></tr>";
				while ($arow = $this->db->sql_fetchrow ($result2)) {
					// print_r ($arow);
					// echo ("<br>");
					$code = $arow['term_id'];
					$vari = $arow['variants'];
					$term = $arow['term'];
					$desc = $arow['description'];
					$pict = $arow['picture'];
					$corps .= "\n<tr class=\"deg\">";
					$corps .= "<td class=\"deg0\" id=\"$code\"><b>$term</b></td>";
					$corps .= "<td class=\"deg0\">$desc</td>";
					// Lien cliquable si l'image est différente de nopict.
					// Clickable link if picture != nopict
					if ($pict != "nopict") {
						$params  = "mode=glosspict&pict=$pict&terme=$term";
						$url = append_sid ($phpbb_root_path."app.php/gloss", $params);
						$corps .= "<td class=\"deg1\"><a href=\"$url\">$pict</a></td>";
						}
					else {
						$corps .= "<td class=\"deg1\">$pict</td>";
						}
					$corps .= "<td class=\"deg1\">";
					$corps .= "<a href=\"";
					$params = "mode=glossedit&code=$code&action=edit";
					$corps .= append_sid ($phpbb_root_path."app.php/gloss", $params);
					$corps .= "\">$str_edit</a></td>";
					$corps .= "</tr>";
					}	// Fin du while sur le contenu
				$this->db->sql_freeresult ($result2);
				}	// Fin du while sur les initiales
			$this->db->sql_freeresult ($result);
			$corps .= "</table>";
			$abc_links .= "</p>\n";

			// Information sur le lien d'édition et de création
			$str_ici = $this->user->lang['GLOSS_ED_ICI'];
			$illustration  = $this->user->lang['GLOSS_ED_EXPL'];
			$illustration .= "<a href=\"";
			$illustration .= append_sid ($phpbb_root_path."app.php/gloss", "mode=glossedit&code=-1&action=edit");
			$illustration .= "\"><b>$str_ici</b></a>.";		
			break;
		}	// Fin du switch sur action
		
	// Appel de l'en-tête en spécifiant un titre et l'onglet du navigateur
	$titre = $this->user->lang['TGLOSSAIRE'];
	page_header($titre);
	
	$this->template->set_filenames (array(
		'body' => 'gloss/glossaire.html',
	));
	$this->template->assign_vars (array (
		'U_TITRE'			=> $titre,
		'U_ABC'			=> $abc_links,
		'U_ILLUST'		=> $illustration,
		'U_CORPS'			=> $corps,
		'U_BIBLIO'		=> $biblio,
		));

	make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
	page_footer();
	}		
	
	// Uploading function for phpBB 3.1.x
	function upload_31x () 
	{
		global $phpbb_root_path, $phpEx;
		$errors = array ();
		include_once($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
		// Set upload directory
		$upload_dir = $this->ext_path_web . 'glossaire';
		$upload_dir = str_replace(array('../', '..\\', './', '.\\'), '', $upload_dir);
		// Upload file
		$upload = new \fileupload();
		$upload->set_allowed_extensions(array('jpg'));
		$upload->set_allowed_dimensions(false, false, 800, 800);
		$file = $upload->form_upload('upload_file');
		$file->move_file($upload_dir, true);
		if (sizeof($file->error))
		{
			$file->remove();
			$file_error = $file->error;
			$errors = array_merge($errors, $file_error);
		}
		if (!sizeof($errors))
		{
			// phpbb_chmod doesn't work well here on some servers so be explicit
			@chmod($this->ext_path_web . 'glossaire/' . $file->uploadname, 0644);
		}
		return ($file->uploadname);
	}

	// Uploading function for phpBB 3.2.x
	function upload_32x () 
	{
		global $phpbb_root_path, $phpEx;
		$errors = array ();
		// Set upload directory
		$upload_dir = $this->ext_path_web . 'glossaire';
		$upload_dir = str_replace(array('../', '..\\', './', '.\\'), '', $upload_dir);
		/** @var \phpbb\files\upload $upload */
		$upload = $this->files_factory->get('upload')
			->set_error_prefix('LMDI_GLOSS_')
			->set_allowed_extensions(array('jpg'))
			->set_allowed_dimensions(false, false, 800, 800);
		// Upload from a form, form name
		$file = $upload->handle_upload ('files.types.form', 'upload_file');
		$filename = $file->get('realname');
		if (sizeof($file->error))
		{
			$file->remove();
			$errors = array_merge($errors, $file->error);
			return false;
		}
		$file->move_file($upload_dir, true);
		return ($filename);
	}
}
?>
