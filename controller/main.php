<?php
/**
*
* @package phpBB Extension - LMDI Glossary extension
* @copyright (c) 2015 LMDI - Pierre Duhem
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace lmdi\gloss\controller;

class main
{
	protected $glossaire;
	/** @var \phpbb\template\template */
	protected $template;
	/** @var \phpbb\user */
	protected $user;
	/** @var \phpbb\auth\auth */
	protected $auth;
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;
	/** @var \phpbb\request\request */
	protected $request;
	/** @var \phpbb\config\config */
	protected $config;
	/** @var \phpbb\controller\helper */
	protected $helper;
	/** @var string phpBB root path */
	protected $phpbb_root_path;
	/** @var string phpEx */
	protected $phpEx;

	/**
	* Constructor
	*
	* @param \phpbb\template\template		 	$template
	* @param \phpbb\user						$user
	* @param \phpbb\auth\auth					$auth
	* @param \phpbb\db\driver\driver_interface	$db
	* @param \phpbb\request\request		 		$request
	* @param \phpbb\config\config				$config
	* @param \phpbb\controller\helper		 	$helper
	* @param string 							$phpbb_root_path
	* @param string 							$phpEx
	*
	*/
	public function __construct(
		\lmdi\gloss\core\glossaire $glossaire,
		\lmdi\gloss\core\glossedit $glossedit,
		\lmdi\gloss\core\glosspict $glosspict,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\auth\auth $auth,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\request\request $request,
		\phpbb\config\config $config,
		\phpbb\controller\helper $helper,
		$phpbb_root_path,
		$phpEx)
	{
		$this->glossaire		 	= $glossaire;
		$this->glossedit		 	= $glossedit;
		$this->glosspict		 	= $glosspict;
		$this->template 			= $template;
		$this->user 				= $user;
		$this->auth 				= $auth;
		$this->db 				= $db;
		$this->request 			= $request;
		$this->config 				= $config;
		$this->helper 				= $helper;
		$this->phpbb_root_path 		= $phpbb_root_path;
		$this->phpEx 				= $phpEx;
	}
	public function handle_gloss()
	{
		include($this->phpbb_root_path . 'includes/functions_user.' . $this->phpEx);
		include($this->phpbb_root_path . 'includes/functions_module.' . $this->phpEx);
		include($this->phpbb_root_path . 'includes/functions_display.' . $this->phpEx);

		// Exclude Bots
		if ($this->user->data['is_bot'])
		{
			redirect(append_sid("{$this->phpbb_root_path}index.{$this->phpEx}"));
		}
		
		// Variables
		$mode = $this->request->variable('mode', '');
		$action = $this->request->variable('action', '');
		$code = $this->request->variable('code', '-1');
		// var_dump ($mode);
		// var_dump ($code);
		// var_dump ($action);
		
		// String loading
		$this->user->add_lang_ext('lmdi/gloss', 'gloss');

		// Add the base entry into the Nav Bar at top
		$this->template->assign_block_vars('navlinks', array(
			'U_VIEW_FORUM'	=> $this->helper->route('lmdi_gloss_controller'),
			'FORUM_NAME'	=> $this->user->lang['LGLOSSAIRE'],
			'code'		=> $code,
		));

		switch($mode)
		{
			case 'glosspict':
				$this->glosspict->main();
			break;
			case 'glossedit':
				$this->glossedit->main();
			break;
			default:
				$this->glossaire->main();
			break;
		}
	}
}