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
	 * Display the module
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
		if (!FE_USER_LOGGED_IN)
		{
			return;
		}

		// Import the User and look for all allowed mail accounts
		$this->import(FrontendUser, User);
		$objMailaccounts = \UberspacempcModel::findAuthorizedMailboxAccounts($this->User->id);

		if (!is_object($objMailaccounts))
		{
			return;
		}

		$arrMailboxOptions = array();
		while ($objMailaccounts->next()) 
		{
			$arrMailboxOptions[$objMailaccounts->id] = $objMailaccounts->email;
		}

		// Create the Template for the module
		$this->strTemplate = 'mod_uberspacempc';
		$this->Template = new \FrontendTemplate($this->strTemplate);

		// Declare the fields for the FE-Module
		$arrFields = array
		(
			'mailbox' => array
			(
				'name'                    => 'mailbox',
				'label'                   => &$GLOBALS['TL_LANG']['UberspaceMPC']['mailboxOptionLegend'],
				'options'                 => $arrMailboxOptions,
				'inputType'               => 'select',
				'eval'                    => array('mandatory'=>true)
			),
			'password' => array
			(
				'name'                    => 'password',
				'label'                   => &$GLOBALS['TL_LANG']['MSC']['password'],
				'inputType'               => 'password',
				'eval'                    => array('mandatory'=>true, 'preserveTags'=>true, 'minlength'=>$GLOBALS['TL_CONFIG']['minPasswordLength']),
			)
		);


		$strFields = '';
		$doNotSubmit = false;
		$strFormId = 'tl_UberspaceMPC_' . $this->id;

		// Initialize the widgets
		foreach ($arrFields as $arrField)
		{
			$strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];

			// Continue if the class is not defined
			if (!class_exists($strClass))
			{
				continue;
			}

			$arrField['eval']['tableless'] = true;
			$arrField['eval']['required'] = $arrField['eval']['mandatory'];

			$objWidget = new $strClass($strClass::getAttributesFromDca($arrField, $arrField['name']));
			$objWidget->storeValues = true;

			// Validate the widget
			if (\Input::post('FORM_SUBMIT') == $strFormId)
			{
				$objWidget->validate();

				if ($objWidget->hasErrors())
				{
					$doNotSubmit = true;
				}
			}

			$strFields .= $objWidget->parse();
		}

		$this->Template->fields = $strFields;
		$this->Template->formID = $strFormId;
		$this->Template->action = \Environment::get('indexFreeRequest');
		$this->Template->slabel = $GLOBALS['TL_LANG']['UberspaceMPC']['slabel'];


		if (\Input::post('FORM_SUBMIT') == $strFormId && !$doNotSubmit)
		{
			$objAccount = \UberspacempcModel::findEntry(\Input::post('mailbox'), $this->User->id);

			$this->strTemplate = 'mod_message';
			$this->Template = new \FrontendTemplate($this->strTemplate);

			if (is_object($objAccount))
			{
				if($this->setNewPassword($objAccount->username, $_POST[password]))
				{
					$this->Template->type = 'success';
					$this->Template->message = $GLOBALS['TL_LANG']['UberspaceMPC']['success'];
					return;
				}
			}
			$this->Template->type = 'error';
			$this->Template->message = $GLOBALS['TL_LANG']['UberspaceMPC']['error'];
		}
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