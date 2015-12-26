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
	'GLOSS_BIBLIO'		=> "
		<p class=\"m\">
		<u>Bibliographie</u><br /> 
		<br /><br /> 
		<u>Webographie</u><br /><br />
		<br />
		<u>Illustrations</u><br /><br />
		<br /> 
		</p>",
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
// Installation
	'ROLE_A_LMDI_GLOSSARY'	=> 'Administrateurs du glossaire',
	'ROLE_U_LMDI_GLOSSARY'	=> 'Éditeurs du glossaire',
	'ROLE_U_LMDI_DESC'		=> 'Modèle des utilisateurs chargés de l\'édition du glossaire',
	'ROLE_A_LMDI_DESC'		=> 'Modèle des administrateurs chargés de gérer le glossaire et ses éditeurs',
// ACP
	'ACP_GLOSS_TITLE'	=> 'Glossaire',
	'GLOSS_PAGE'		=> 'Glossaire',
	'ACP_LEXICON'	=> 'Glossaire entomologique',
	'ACP_LEXICON_EXPLAIN'	=> 'Paramétrage du glossaire',
	'ACP_GLOSS_TITLE'	=> 'Glossaire entomologique',
	'ACP_GLOSS'	=> 'Paramétrage',
     'ALLOW_FEATURE'        => 'Validation de la fonction de glossaire',
     'ALLOW_FEATURE_EXPLAIN'        => 'Vous pouvez valider ou inhiber la fonction au niveau du forum. Si vous validez, l\'utilisateur pourra s\'il le souhaite inhiber l\'affichage des termes du glossaire dans les messages du forum (à partir du panneau de l\'utilisateur).',
	'ALLOW_TITLE'        => 'Validation des infobulles',
     'ALLOW_TITLE_EXPLAIN'        => 'Vous pouvez valider ou inhiber l\'affichage d\'une infobulle lorsque le curseur passe au-dessus du terme dans les messages du forum. Si la description est très longue, elle est tronquée à 50 caractères.',
	'GLOSSARY_ADMINISTRATORS'	=> 'Administrateurs du glossaire',
	'GLOSS_GROUP_DESC_A'		=> 'Groupe des administrateurs du glossaire',
	'GLOSSARY_EDITORS'	=> 'Éditeurs du glossaire',
	'GLOSS_GROUP_DESC_U'		=> 'Groupe des éditeurs du glossaire',
	'CREATE_UGROUP'		=> 'Création d\'un groupe d\'utilisateurs',
	'CREATE_UGROUP_EXPLAIN'	=> 'Vous pouvez créer un groupe d\'utilisateurs auquel vous attribuerez le rôle d\'éditeur des rubriques du glossaire qui a été créé lors de l\'installation de l\'extension. Vous pouvez ensuite placer dans ce groupe les utilisateurs chargés de cette tâche.',
	'CREATE_AGROUP'		=> 'Création d\'un groupe d\'administrateurs',
	'CREATE_AGROUP_EXPLAIN'	=> 'Vous pouvez créer un groupe pour gérer les administrateurs du glossaire. Vous pouvez ensuite y ajouter les administrateurs sélectionnés.',
	'LANGUAGE'		=> 'Langue par défaut',
	'LANGUAGE_EXPLAIN'	=> 'Code de langue (par défaut langue du forum) qui est enregistré si vous ne spécifiez pas une autre langue dans le formulaire de saisie.',
// UCP
	'UCP_GLOSS_TITLE'	=> 'Glossaire entomologique',
	'UCP_GLOSS_MANAGE'	=> 'Glossaire entomologique',
	'UCP_CONFIG_SAVED'	=> 'La configuration a bien été enregistrée.<br /><br />%sCliquez ici pour revenir à la page précédente.%s',
	'UCP_GLOSS'		=> 'Page principale',
	'UCP_ENABLE'		=> 'Validation de la fonction de glossaire',
	'UCP_ENABLE_EXPLAIN' => 'Le glossaire affiche une explication (et éventuellement une illustration) lorsque vous cliquez sur les termes techniques dans les sujets du forum.',
	
     ));
