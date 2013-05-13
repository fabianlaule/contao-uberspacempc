<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (c) 2005-2013 Leo Feyer
 *
 * @package   UberspaceMPC
 * @author    Fabian Laule <fabianlaule.de>
 * @license   LGPL
 * @copyright Fabian Laule 2013
 */


/**
 * Namespace
 */
namespace UberspaceMPC;

/**
 * Class ModuleUberspaceMPC
 *
 * @copyright  Fabian Laule 2013
 * @author     Fabian Laule <fabianlaule.de>
 * @package    Devtools
 */
class ModuleUberspaceMPC extends \Module
{

	/**
	 * Errors
	 * @var array
	 */
	protected $arrErrors = array();
	

	/**
	 * Display a login form
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new \BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### UberspaceMPC ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}

	/**
	 * Generate the module
	 */
	protected function compile()
	{
		// Check if the User is logged in
		if(FE_USER_LOGGED_IN)
		{
			$this->import(FrontendUser, User);
			
			// Looking for all allowed mail accounts
			$objMailaccounts = \UberspacempcModel::findAuthorizedMailboxAccounts($this->User->id);
		
			// Is there a object with mailbox accounts? if not, return
			if(!is_object($objMailaccounts))
			{
				return;
			}

			// Create a (new) Template for the module
			$this->Template = new \FrontendTemplate('mod_uberspacempc');
	
			if(\Input::post('FORM_SUBMIT') == 'tl_UberspaceMPC')
			{
				$objMailaccount = \UberspacempcModel::findEntry(\Input::post('UberspaceMPC_radio'), $this->User->id);

				// Check if the submitted MailboxID is a valid and authorized ID
				if(!$objMailaccount)
				{
					$this->addError($GLOBALS['TL_LANG']['UberspaceMPC']['noValidMailbox']);
				}

				// Validate the password
				$this->validatePassword(\Input::postRaw('UberspaceMPC_password'), \Input::postRaw('UberspaceMPC_password_confirm'));
			
				// If everything is ok, set the new password
				if($this->getAllErrors() == '')
				{
					$result = $this->setNewPassword($objMailaccount->username, $_POST[UberspaceMPC_password]);
					
					if($result)
					{
						$this->Template->success = $GLOBALS['TL_LANG']['UberspaceMPC']['success'];
					}
					else
					{
						$this->addError($GLOBALS['TL_LANG']['UberspaceMPC']['setPasswordError']);
						$this->log('Setting a new password for ' . $objMailaccount->email . ' failed.', 'ModuleUberspaceMPC setNewMailboxPassword()', TL_ERROR);
					}
				}
			}

			// Assign variables
			$this->Template->arrMailboxes = $objMailaccounts;
			$this->Template->error = $this->getAllErrors();
			$this->Template->action = $this->getIndexFreeRequest();
			$this->Template->radioLegend = $GLOBALS['TL_LANG']['UberspaceMPC']['radioLegend'];
			$this->Template->explanation = $GLOBALS['TL_LANG']['UberspaceMPC']['explanation'];
			$this->Template->passwordLabel = $GLOBALS['TL_LANG']['UberspaceMPC']['passwordLabel'];
			$this->Template->password_confirmLabel = $GLOBALS['TL_LANG']['UberspaceMPC']['password_confirmLabel'];
			$this->Template->slabel = $GLOBALS['TL_LANG']['UberspaceMPC']['slabel'];
		}
	}

	/**
	 * Add an error message
	 * 
	 * @param string $strError The error message
	 */
	protected function addError($strError)
	{
		$this->class = 'error';
		$this->arrErrors[] = $strError;
	}
	
	/**
	 * Return all error messages as an array
	 */
	protected function getAllErrors()
	{
		$strErrors = '';
		foreach ($this->arrErrors as $Error) {
			$strErrors .= $Error . '<br />';
		}
		return $strErrors;
	}
	
	/**
	 * Validates the submitted password
	 * 
	 * @param string $varPassword The password
	 * @param string $varPasswordConfirm The confirmation password
	 */
	protected function validatePassword($varPassword, $varPasswordConfirm)
	{
		$blnisValid = true;	
		
		if (utf8_strlen($varPassword) < $GLOBALS['TL_CONFIG']['minPasswordLength'])
		{
			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['passwordLength'], $GLOBALS['TL_CONFIG']['minPasswordLength']));
			$blnisValid = false;
		}

		if ($varPassword !== $varPasswordConfirm)
		{
			$this->addError($GLOBALS['TL_LANG']['ERR']['passwordMatch']);
			$blnisValid = false;
		}
		
		return $blnisValid;
	}
	
	/**
	 * set the new Password via Shell
	 * 
	 * @param string $strMailbox The password
	 * @param string $strPassword The confirmation password
	 */
	function setNewPassword($strMailbox, $strPassword)
	{
		$strPassword = utf8_decode($strPassword);
		$strCommand = 'vpasswd ' . $strMailbox;
		
		$descriptorspec = array(
				0 => array("pipe", "r")
		);
		
		$process = proc_open($strCommand, $descriptorspec, $pipes, NULL, NULL);
		
		if(is_resource($process))
		{
			fwrite($pipes[0], $strPassword);
			fclose($pipes[0]);
			$return_value = proc_close($process);
				if($return_value == 0)
				{
					return true;	
				}
		}
		return false;
	}
}