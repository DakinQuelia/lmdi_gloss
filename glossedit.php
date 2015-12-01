<?php
// glossedit.php
// (c) Pierre Duhem - LMDI - 2015
// Version de glossaire.php utilisée pour les animateurs et administrateurs
// Voir aussi les commentaires du script glossaire.php

/*	Historique
	30/10/15 - Il serait bon, après l'enregistrement d'une modification d'une rubrique
			 du glossaire, de revenir au moins à l'initiale dans la liste, plutôt
			 qu'au début.
	Fin de l'historique
	*/
	
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
$phpbb_root_path = "./../../../";
include($phpbb_root_path . 'common.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();
$user->add_lang_ext('lmdi/gloss', 'gloss');
// var_dump ($user->lang);

$abc_links = "";
$illustration = "";
$corps = "";
$biblio = "";
$table = $table_prefix . "glossary";

$num    = request_var ('code', 0);
$action = request_var ('action', "rien");
$delete = request_var ('delete', "rien");
$save   = request_var ('save', "rien");
if ($delete != 'rien')
	$action = 'delete';
if ($save != 'rien')
	$action = 'save';

$str_colon = $user->lang['COLON'];

switch ($action) {
	case "edit" :
		if ($num < 0) {	// Création d'une fiche
			$code = "";
			$vari = "";
			$term = "";
			$desc = "";
			$pict = "";
			$lang = "fr";
			$str_action = $user->lang['GLOSS_CREAT'];
			}
		else {			// Édition d'une fiche
			$sql  = "SELECT * ";
			$sql .= "FROM $table ";
			$sql .= "WHERE term_id = \"$num\" ";
			// echo ("Valeur de la requête : $sql.<br>\n");
			$result = $db->sql_query ($sql);
			$row = $db->sql_fetchrow ($result);
			$code = $row['term_id'];
			$vari = $row['variants'];
			$term = $row['term'];
			$desc = $row['description'];
			$pict = $row['picture'];
			$lang = $row['lang'];
			$db->sql_freeresult ($result);
			$str_action = $user->lang['GLOSS_EDIT'];
			}
		$str_variants = $user->lang['GLOSS_VARIANTS'] . $str_colon;
		$str_terme = $user->lang['GLOSS_TERM'] . $str_colon;
		$str_varex = $user->lang['GLOSS_VARIANTS_EX'];
		$str_terex = $user->lang['GLOSS_TERM_EX'];
		$str_desc  = $user->lang['GLOSS_DESC'] . $str_colon;
		$str_pict  = $user->lang['GLOSS_PICT'] . $str_colon;
		$str_pictex= $user->lang['GLOSS_ED_PIEXPL'];
		$str_regis = $user->lang['GLOSS_REGIS'];
		$str_suppr = $user->lang['GLOSS_SUPPR'];
		$form  = "<form action=\"";
		$form .= append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx);
		$form .= "\" method=\"post\" id=\"glossedit\">";
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
		$form .= "<dt><label for=\"pict\">$str_pict</label><br />";
		$form .= "<span>$str_pictex</span></dt>";
		$form .= "<dd><input type=\"text\" tabindex=\"4\" name=\"pict\" ";
		$form .= "id=\"pict\" size=\"25\" value=\"$pict\" class=\"inputbox autowidth\" /></dd>";
		$form .= "</dl>";
		$form .= "<dl>";
		$form .= "<dl><dt>&nbsp;</dt>";
		$form .= "<dd><input type=\"submit\" name=\"save\" id=\"save\" tabindex=\"5\" value=\"$str_regis\" class=\"button1\" />&nbsp;&nbsp;";
		$form .= "<input type=\"submit\" name=\"delete\" id=\"delete\" tabindex=\"6\" value=\"$str_suppr\" class=\"button1\" /></dd>";
		$form .= "</dl>";
		$form .= "</fieldset>";
		$form .= "</div></div></div>";
		$abc_links = $form;
		break;
	case "save" :		// Enregistrement
		$term_id     = $db->sql_escape (request_var ('term_id', 0));
		$term        = $db->sql_escape (request_var ('term', "", true));
		$variants    = $db->sql_escape (request_var ('vari', "", true));
		$description = $db->sql_escape (request_var ('desc', "", true));
		$picture     = $db->sql_escape (request_var ('pict', "", true));
		if (!strlen ($picture))
			$picture = "nopict";
		$lang        = $db->sql_escape (request_var ('lang', "fr", true));
		if ($term_id == 0) {
			$sql  = "INSERT INTO $table ";
			$sql .= "(variants, term, description, picture, lang) ";
			$sql .= " VALUES ";
			$sql .= "(\"$variants\", \"$term\", \"$description\", \"$picture\", \"$lang\")";
			// echo ("Valeur de la requête : $sql.<br>\n");
			$db->sql_query ($sql);	
			$term_id = $db->sql_nextid();
			}
		else {
			$sql  = "UPDATE $table SET ";
			$sql .= "term_id       = \"$term_id\", ";
			$sql .= "variants      = \"$variants\", ";
			$sql .= "term          = \"$term\", ";
			$sql .= "description   = \"$description\", ";
			$sql .= "picture       = \"$picture\", ";
			$sql .= "lang          = \"$lang\" ";
			$sql .= "WHERE term_id = \"$term_id\" ";
			$sql .= "LIMIT 1";
			// echo ("Valeur de la requête : $sql.<br>\n");
			$db->sql_query ($sql);	
			}	
		$params = "#$term_id";
		$url  = append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx, $params);
		header("Location:$url");
		exit ();
		break;
	case "delete" :
		$term_id     = $db->sql_escape (request_var ('term_id', 0));
		$sql  = "DELETE ";
		$sql .= "FROM $table ";
		$sql .= "WHERE term_id = \"$term_id\" ";
		$sql .= "LIMIT 1";
		// echo ("Valeur de la requête : $sql.<br>\n");
		$db->sql_query ($sql);	
		$cap = substr (request_var ('term', "", true), 0, 1);
		$params = "#$cap";
		$url  = append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx, $params);
		header("Location:$url");
		exit ();
		break;
	case "rien" :
		$sql  = "SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a ";
		$sql .= "FROM $table ";
		// $sql .= " WHERE lang = '" . $this->user->lang['USER_LANG'] . "'";
		$sql .= " ORDER BY a" ;
		// echo ("Valeur de la requête : $sql.<br>\n");
		$result = $db->sql_query ($sql);

		$str_titre = $user->lang['GLOSS_EDITION'];
		$str_terme = $user->lang['GLOSS_ED_TERM'];
		$str_defin = $user->lang['GLOSS_ED_DEF'];
		$str_illus = $user->lang['GLOSS_ED_PICT'];
		$str_action = $user->lang['GLOSS_ED_ACT'];
		$abc_links = "<span id=\"haut\"></span>\n";
		$abc_links .= "<h2 class=\"login-title\">$str_titre</h2>";
		$abc_links .= "<p class=\"glossa\">";
		
		$corps  = "<table class=\"deg\"><tr class=\"deg\">";
		$corps .= "<th class=\"deg0\">$str_terme</th>";
		$corps .= "<th class=\"deg0\">$str_defin</th>";
		$corps .= "<th class=\"deg1\">$str_illus</th>";
		$corps .= "<th class=\"deg1\">$str_action</th></tr>";
		$cpt  = 0;


		$str_edit  = $user->lang['GLOSS_ED_EDIT'];
		while ($row = $db->sql_fetchrow ($result)) {
			// print_r ($row);
			$l = $row['a'];
			$abc_links .= "<a href =\"#$l\">$l</a> " ;

			$sql  = "SELECT * ";
			$sql .= "FROM $table ";
			$sql .= "WHERE LEFT($table.term, 1) = \"$l\" ";
			// $sql .= "WHERE lang = '" . $user->lang['USER_LANG'] . "' ";
			$sql .= "ORDER BY term";
			// echo ("Valeur de la requête : $sql.<br>\n");
			$result2 = $db->sql_query ($sql);

			$cpt++;
			$corps .= "\n<tr class=\"deg\"><td class=\"glossi\" colspan=\"3\" id=\"$l\">&nbsp;$l</td>";
			$corps .= "<td class=\"haut\"><a href=\"#haut\"><img src=\"styles/top.gif\"></a></td></tr>";
			while ($arow = $db->sql_fetchrow ($result2)) {
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
				//	Lien cliquable si l'image est différente de nopict.
				if ($pict != "nopict") {
					$params = "url=glossaire/{$pict}.jpg&terme=$term";
					$url = append_sid ("glosspict.php", $params);
					$corps .= "<td class=\"deg1\"><a href=\"$url\">$pict</a></td>";
					}
				else {
					$corps .= "<td class=\"deg1\">$pict</td>";
					}
				
				$corps .= "<td class=\"deg1\">";
				$corps .= "<a href=\"";
				$corps .= append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx, "code=$code&action=edit");
				$corps .= "\">$str_edit</a></td>";
				$corps .= "</tr>";
				}	// Fin du while sur le contenu
			$db->sql_freeresult ($result2);
			}	// Fin du while sur les initiales
		$db->sql_freeresult ($result);
		// Fermeture de la table
		$corps .= "</table>";
		$abc_links .= "</p>\n";

		// Information sur le lien d'édition et de création
		$str_ici = $user->lang['GLOSS_ED_ICI'];
		$illustration  = $user->lang['GLOSS_ED_EXPL'];
		$illustration .= "<a href=\"";
		$illustration .= append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx, "code=-1&action=edit");
		$illustration .= "\"><b>$str_ici</b></a>.";		
		break;
	}	// Fin du switch sur action
	
// Appel de l'en-tête en spécifiant un titre et l'onglet du navigateur
page_header('Glossaire entomologique');

// Appel du gabarit défini dans le dossier styles/lmdi31/template
$template->set_filenames (array (
    'body' => 'glossaire.html',
));

// Passage des chaînes à afficher
$template->assign_vars (array (
	'U_TITRE'			=> $user->lang['ACP_LEXICON'],
	'U_ABC'			=> $abc_links,
	'U_ILLUST'		=> $illustration,
	'U_CORPS'			=> $corps,
	'U_BIBLIO'		=> $biblio,
));

make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>
	