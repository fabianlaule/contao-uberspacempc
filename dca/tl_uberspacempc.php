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
 * Table tl_uberspacempc
 */
$GLOBALS['TL_DCA']['tl_uberspacempc'] = array
( 
	
	// Config
	'config' => array
	(
		'dataContainer'               => 'Table',
		'enableVersioning'            => true,
		'sql' => array
		(
			'keys' => array
			(
				'id' => 'primary'
			)
		)
	),

	// List
	'list' => array
	(
		'sorting' => array
		(
			'mode'					=> 2,
			'fields'				=> array('email'),
			'flag' 					=> 1,
			'panelLayout'			=> 'search,sort;limit',
			'disableGrouping'		=> true,
		),
		'label' => array
		(
			'fields'				=> array('email'),
			'format'				=> '%s',
			'label_callback'		=> array('tl_uberspacempc', 'addInfo')
		),
		'global_operations' => array
		(
			'all' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['MSC']['all'],
				'href'				=> 'act=select',
				'class'				=> 'header_edit_all',
				'attributes'		=> 'onclick="Backend.getScrollOffset();" accesskey="e"'
			)
		),
		'operations' => array
		(
			'edit' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['edit'],
				'href'				=> 'act=edit',
				'icon'				=> 'edit.gif'
			),
			'delete' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['delete'],
				'href'				=> 'act=delete',
				'icon'				=> 'delete.gif',
				'attributes'		=> 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"'
			),
			'show' => array
			(
				'label'				=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['show'],
				'href'				=> 'act=show',
				'icon'				=> 'show.gif'
			)
		)
	),

	// Palettes
	'palettes' => array
	(
		'default'					=> '{mailboxDetails_legend},username,email;{authorizedFrontendUsers_legend},authorizedFrontendUsers'
	),

	// Fields
	'fields' => array
	(
		'id' => array
		(
			'sql'					=> "int(10) unsigned NOT NULL auto_increment"
		),
		'tstamp' => array
		(
			'sql'					=> "int(10) unsigned NOT NULL default '0'"
		),
		'email' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['email'],
			'exclude'				=> true,
			'search'				=> true,
			'sorting'				=> true,	
			'inputType'				=> 'text',
			'eval'					=> array('mandatory'=>true, 'unique'=>true, 'maxlength'=>255, 'tl_class'=>'w50'),
			'sql'					=> "varchar(255) NOT NULL default ''"
		),
		'username' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['username'],
			'exclude'				=> true,
			'search'				=> true,
			'sorting'				=> true,
			'inputType'				=> 'select',
			'options_callback'		=> array('tl_uberspacempc', 'getUsernames'),
			'eval'					=> array('chosen'=>true, 'unique'=>true, 'tl_class'=>'w50'),
			'sql'					=> "varchar(255) NOT NULL default ''"
		),
		'authorizedFrontendUsers' => array
		(
			'label'					=> &$GLOBALS['TL_LANG']['tl_uberspacempc']['authorizedFrontendUsers'],
			'exclude'				=> false,
			'inputType'				=> 'select',
			'options_callback'		=> array('tl_uberspacempc', 'getFrontendUsers'), 
			'eval'					=> array('includeBlankOption'=>true, 'mandatory'=>true, 'multiple' =>true, 'chosen'=>true),
			'sql'					=> "varchar(255) NOT NULL default ''"
		)
	)
);


/**
 * Class tl_uberspacempc
 *
 * @copyright  Fabian Laule 2013
 * @author     Fabian Laule <fabianlaule.de>
 * @package    Devtools
 */
class tl_uberspacempc extends \Backend
{
	/**
	 * Get all usernames of the available mailboxes
	 * @param object
	 * @return array
	 */
	public function getUsernames()
	{
		$arrUsernames = array();

		// Back end
		if (TL_MODE == 'BE')
		{
			// get the available usernames
			$usernames = shell_exec('listvdomain');
			$usernames = preg_split('/[\r\n]+/', $usernames, NULL, PREG_SPLIT_NO_EMPTY);
			// we don't need the first line (the output is like a table and there are the headings in the first part of the array)
			unset($usernames[0]);

			foreach ($usernames as $key => $value)
			{
				$value = explode(" ", $value);
				$arrUsernames[$value[0]] = $value[0];
			}

			return $arrUsernames;
		}

		return array();
	}

	/**
	 * Get all FrontendUsers and return them as array
	 * @param object
	 * @return array
	 */
	public function getFrontendUsers($objModule)
	{
		$objFrontendUsers = MemberModel::findAll();

		if ($objFrontendUsers === null)
		{
			return array();
		}

		$arrFrontendUsers = array();

		// Back end
		if (TL_MODE == 'BE')
		{
			while ($objFrontendUsers->next())
			{
				$arrFrontendUsers[$objFrontendUsers->id] = "$objFrontendUsers->firstname $objFrontendUsers->lastname ($objFrontendUsers->email)";
			}

			return $arrFrontendUsers;
		}

		return array();
	}

	/**
	 * Add more informations to each record
	 * @param array
	 * @param string
	 * @return string
	 */
	public function addInfo($row, $label)
	{
		return sprintf(
					'%s <span style="color:#b3b3b3;font-style:italic;padding-left:5px">%s</span>',
					$label, 
					$GLOBALS['TL_LANG']['tl_uberspacempc']['username'][0] . ': ' . $row['username']
					);
	}
}
