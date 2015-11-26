<?php
// glossaire.php
// (c) Pierre Duhem - LMDI - 2015
/* 	Essai de codage d'une page statique de glossaire.
	La table utilisée est phpbb3_lexicon.
	La structure (historique) est la suivante :
	`term_id` mediumint(9) NOT NULL,
	`variants` varchar(80) NOT NULL DEFAULT '',
	`term` varchar(80) DEFAULT NULL,
	`description` varchar(255) NOT NULL DEFAULT '',
	`picture` varchar[80],
	`lang` varchar(15) NOT NULL DEFAULT 'fr'
	Observations :
	Les zones variants et term contiennent la même chose, ou à peu près. La zone variants
	est en minuscules, parce que c'est la chaîne recherchée dans le sujet pour la
	remplacer par le lien cliquable. La zone term est par contre utilisée pour
	afficher le résultat, donc avec une majuscule initiale.
	La zone lang contient la langue. 'fr' pour le français. C'est la valeur par
	défaut. Il faudrait être plus universel...
	La zone picture a dû servir à je ne sais quoi dans le passé. Actuellement, elle
	contient le nom d'un fichier d'illustration, sans chemin d'accès (par défaut :
	ext/lmdi/gloss/glossaire/nom_fichier) et sans extension (par défaut jpg).
	*/
	
/*	Recherche les lettres présentes dans la table glossary, pour la langue de
	l'utilisateur. Requête :
	// SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a FROM phpbb3_lexicon
     // WHERE lang = 'fr' ORDER BY a
     Il est également possible d'avoir une interface pour un glossaire francophone,
     dont seuls les titres sont traduits en anglais. Dans ce cas, on utilise :
	// SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a FROM phpbb3_lexicon
     // ORDER BY a (supprimer le commentaire dans les deux interrogations)
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

$table = $table_prefix . "glossary";

$sql  = "SELECT DISTINCT UPPER(LEFT(TRIM(term),1)) AS a ";
$sql .= "FROM $table ";
// $sql .= " WHERE lang = '" . $this->user->lang['USER_LANG'] . "'";
$sql .= " ORDER BY a" ;
// echo ("Valeur de la requête : $sql.<br>\n");
$result = $db->sql_query ($sql);

$abc_links = "<span id=\"haut\"></span>\n<p class=\"glossa\">";

$str_terme = $user->lang['GLOSS_ED_TERM'];
$str_defin = $user->lang['GLOSS_ED_DEF'];
$str_illus = $user->lang['GLOSS_ED_PICT'];
$str_action = $user->lang['GLOSS_DISPLAY'];

$corps  = "<table class=\"deg\"><tr class=\"deg\">";
$corps .= "<th class=\"deg0\">$str_terme</th>";
$corps .= "<th class=\"deg0\">$str_defin</th>";
$corps .= "<th class=\"deg1\">$str_illus</th></tr>";

$cpt  = 0;

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
     $corps .= "<tr class=\"deg\"><td class=\"glossi\" colspan=\"2\" id=$l>&nbsp;$l</td>";
	$corps .= "<td class=\"haut\"><a href=\"#haut\"><img src=\"./styles/prosilver/theme/top.gif\"></a></td></tr>";
     while ($arow = $db->sql_fetchrow ($result2)) {
		// print_r ($arow);
		// echo ("<br>");
          $vari = $arow['variants'];
          $term = $arow['term'];
          $desc = $arow['description'];
          $pict = $arow['picture'];
		$corps .= "<tr class=\"deg\">";
		$corps .= "<td class=\"deg0\"><b>$term</b></td>";
		$corps .= "<td class=\"deg0\">$desc</td>";
          $corps .= "<td class=\"deg1\">";
		/*	Pour chaque ligne du résultat, nous ne mettons un lien cliquable que si
			l'image est différente de pasdimage.
			*/
          if ($pict != "pasdimage") {
			$params = "url=glossaire/{$pict}.jpg&terme=$term";
			$url = append_sid ("glosspict.php", $params);
               $corps .= "<a href=\"$url\">$str_action</a></td>";
               }
          else {
               $corps .= "&nbsp;</td>";
               }
		$corps .= "</tr>";
          }	// Fin du while sur le contenu
	$db->sql_freeresult ($result2);
     }	// Fin du while sur les initiales
$db->sql_freeresult ($result);
// Fermeture de la table
$corps .= "</table>";
// Fermeture de la ligne de liens avec un lien vers la page d'administration
$str_admin = $user->lang['GLOSS_EDITION'];
if ($auth->acl_get('a_')) {
	$abc_links .= " -- <a href=\"";
	$abc_links .= append_sid ($phpbb_root_path."ext/lmdi/gloss/glossedit.".$phpEx);
	$abc_links .= "\">$str_admin</a>";
	}
$abc_links .= "</p>\n";

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
$illustration = $user->lang['ILLUSTRATION'];

page_header('Glossaire entomologique');
$template->set_filenames (array (
    'body' => 'glossaire.html',
));
$template->assign_vars (array (
	'U_TITRE'			=> $user->lang['TGLOSSAIRE'],
	'U_ABC'			=> $abc_links,
	'U_ILLUST'		=> $illustration,
	'U_CORPS'			=> $corps,
	'U_BIBLIO'		=> $biblio,
));


make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>
	