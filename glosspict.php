<?php
// glosspict.php
// (c) Pierre Duhem - LMDI - 2015
// Page d'affichage d'une image centrée complétant le terme du glossaire
// Page displaying a centered picture attached to the glossary term

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

// Début du code utile	
$click = $user->lang['GLOSS_CLICK'];
$view = $user->lang['GLOSS_VIEW'];
$url = request_var ('url', '');
$term = request_var ('terme', '', true);
$terme = "<p class=\"copyright\"><b>$term</b>.</p>";
$corps = "<p class=\"copyright\"><a href=\"javascript:history.go(-1);\"><img src=$url></a></p>";
$retour = "<p class=\"copyright\">$click</p>";
// Fin du code utile
	
// Appel de l'en-tête en spécifiant un titre et l'onglet du navigateur
page_header($view);

// Appel du gabarit défini dans le dossier styles/lmdi31/template
$template->set_filenames (array (
    'body' => 'glosspict.html',
));

// Passage des chaînes à afficher
$template->assign_vars (array (
	'U_TITRE'			=> $view,
	'U_TERME'			=> $terme,
	'U_CORPS'			=> $corps,
	'U_RETOUR'		=> $retour,
));


make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
page_footer();
?>