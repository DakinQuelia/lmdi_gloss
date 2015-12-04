<?php
/**
* gloss.php
* @package phpBB Extension - LMDI Glossary
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge ($lang, array(
// Static Glossary page
    	'LGLOSSAIRE'			=> 'Glossaire',
	'TGLOSSAIRE'			=> 'Glossaire entomologique',
	'ILLUSTRATION'	=>  "Certains termes sont illustrés.<br>Il existe dans ce cas un lien cliquable au bout de la ligne, cliquez dessus pour afficher l'illustration.<br>Sur la page de visualisation, cliquez sur l'image pour revenir.",
	'GLOSS_DISPLAY'	=> 'Afficher',
	'GLOSS_CLICK'		=> 'Cliquez sur l\'image pour revenir à la page précédente.',
	'GLOSS_VIEW'		=> 'Afficheur du glossaire',
// Glossary edition page
	'GLOSS_EDIT'	=>'Édition d\'une fiche du glossaire',
	'GLOSS_CREAT'	=>'Création d\'une fiche du glossaire',
	'GLOSS_VARIANTS' => 'Termes à rechercher',
	'GLOSS_VARIANTS_EX' => 'Un ou plusieurs termes, séparés par des virgules.',
	'GLOSS_TERM'	=> 'Terme affiché',
	'GLOSS_TERM_EX' => 'Terme utilisé comme titre dans la fenêtre.',
	'GLOSS_DESC'	=> 'Définition du terme',
	'GLOSS_PICT'	=> 'Illustration',
	'GLOSS_REGIS'	=> 'Enregistrer',
	'GLOSS_SUPPR'	=> 'Supprimer',
	'GLOSS_EDITION'	=> 'Page d\'administration du glossaire',
	'GLOSS_ED_TERM'	=> 'Terme',
	'GLOSS_ED_DEF'		=> 'Définition',
	'GLOSS_ED_PICT'	=> 'Illustration',
	'GLOSS_ED_PIEXPL'	=> 'Indiquez ici le nom du fichier contenant l\'illustration. Ne mentionnez pas l\'extension, qui doit être *.jpg. Téléchargez le fichier dans le dossier ext/lmdi/gloss/glossaire.',
	'GLOSS_ED_ACT'		=> 'Action',
	'GLOSS_ED_EXPL'	=> 'Un lien d\'édition existe à l\'extrémité de chaque ligne, pour apporter des modifications.<br>Pour créer une nouvelle rubrique, cliquez ',
	'GLOSS_ED_ICI'		=> 'ici',
	'GLOSS_ED_EDIT'	=> 'Éditer',
// ACP
	'ACP_GLOSS_TITLE'	=> 'Glossaire',
	'GLOSS_PAGE'		=> 'Glossaire',
	'ACP_LEXICON'	=> 'Glossaire entomologique',
	'ACP_LEXICON_EXPLAIN'	=> 'Paramétrage du glossaire',
	'ACP_GLOSS_TITLE'	=> 'Glossaire entomologique',
	'ACP_GLOSS'	=> 'Paramétrage',
     'ALLOW_FEATURE'        => 'Validation de la fonction de glossaire',
     'ALLOW_FEATURE_EXPLAIN'        => 'L\'utilisateur pourra valider ou non l\'affichage des termes du glossaire dans les messages du forum (à partir du panneau de l\'utilisateur).',
// UCP
	'UCP_GLOSS_TITLE'	=> 'Glossaire entomologique',
	'UCP_GLOSS'		=> 'Page principale',
	'UCP_ENABLE'		=> 'Validation de la fonction de glossaire',
	'UCP_ENABLE_EXPLAIN' => 'Le glossaire affiche une explication (et éventuellement une illustration) lorsque vous cliquez sur les termes techniques dans les sujets du forum.',
	
     ));
