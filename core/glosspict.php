<?php
// glosspict.php
// (c) Pierre Duhem - LMDI - 2015
// Page d'affichage d'une image centrée complétant le terme du glossaire
// Page displaying a centered picture attached to the glossary term

namespace lmdi\gloss\core;

class glosspict
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
		global $phpbb_root_path, $phpEx, $request;
	
		$click = $this->user->lang['GLOSS_CLICK'];
		$view = $this->user->lang['GLOSS_VIEW'];
		$pict = $request->variable ('pict', '');
		$pict = $this->ext_path_web . "glossaire/" . $pict . ".jpg";
		$term = $request->variable ('term', '', true);
		$terme = "<p class=\"copyright\"><b>$term</b></p>";
		$corps = "<p class=\"copyright\"><a href=\"javascript:history.go(-1);\"><img src=$pict></a></p>";
		$retour = "<p class=\"copyright\">$click</p>";
			
		page_header($view);
		$this->template->set_filenames (array (
		    'body' => 'glossaire.html',
		));

		// Passage des chaînes à afficher
		// String parameters passing
		$this->template->assign_vars (array (
			'U_TITRE'			=> $view,
			'U_ILLUST'		=> $terme,
			'U_CORPS'			=> $corps,
			'U_BIBLIO'		=> $retour,
		));


		make_jumpbox(append_sid("{$phpbb_root_path}viewforum.$phpEx"));
		page_footer();
	}
}
?>