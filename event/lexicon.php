<?php
/**
* @author Renate Regitz http://www.kaninchenwissen.de/
* Version reprise pour l'extension Glossary par Pierre Duhem
* Ce code est utilisé pour extraire de la table glossary le terme correspondant
* au code passé en paramètre. Il renvoie le contenu de la rubrique qui va
* apparaître dans la fenêtre surgissante.
* Extraction of the term from the glossary table. The returned text will
* be the code for the popup window.
**/

define('IN_PHPBB', true);

// Inclusion du fichier common.php (dans la racine)
// Inclusion of common.php (in forum root)
$phpbb_root_path = '../../../../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path. 'common.' . $phpEx);

// Session management
$user->session_begin();
$user->setup();

if (!defined('GLOSSARY_TABLE'))
{
	define('GLOSSARY_TABLE', $table_prefix . 'glossary');
}

$id = $request->variable ('id', '0');
if ($id)
{
	// Search lexicon entry in DB
	$sql = "SELECT * FROM " . GLOSSARY_TABLE .
		" WHERE term_id = '$id' LIMIT 1; ";
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$entry = '<h3><a title="'. $user->lang['CLOSE_WINDOW']. '" id="lexiconClose" href="#">x</a></h3>
		<h3>'.$row['term'].'</h3>'.'
		<p><b>'.$row['description'].'</b></p>
		<p><img src="ext/lmdi/gloss/glossaire/'.$row['picture'].'.jpg"></p>';
	$db->sql_freeresult($result);
}

header('Content-type: text/html; charset=UTF-8');
echo $entry;
