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
 * Class UberspacempcModel
 *
 * @copyright  Fabian Laule 2013
 * @author     Fabian Laule <fabianlaule.de>
 * @package    Devtools
 */
class UberspacempcModel extends \Model
{

	/**
	 * Name of the table
	 * @var string
	 */
	protected static $strTable = 'tl_uberspacempc';

	/**
	 * Find the authorized mailbox account by the FrontendUser-ID
	 * 
	 * @param integer $intFrontendUserId      The numeric FrontendUserID
	 * @param array   $arrOptions An optional options array
	 * 
	 * @return \Model|null The UberspacempcModel or null if there are no authorized e-mail accounts
	 */
	public static function findAuthorizedMailboxAccounts($intFrontendUserId, array $arrOptions=array())
	{
		$t = static::$strTable;
		return static::findBy(
							array("$t.authorizedFrontendUsers LIKE ?"),
							array('%"'.$intFrontendUserId.'"%'),
							array('order'=>"$t.email")
							);
	}
	
	/**
	 * Find an Entry by its id and the FrontendUserID
	 * 
	 * @param integer $intID      The ID of the entry
	 * @param integer $intFrontendUserId      The numeric FrontendUserID
	 * @param array   $arrOptions An optional options array
	 * 
	 * @return \Model|null The UberspacempcModel or null if there is no entry
	 */
	public static function findEntry($intID, $intFrontendUserId, array $arrOptions=array())
	{
		$t = static::$strTable;
		return static::findOneBy(
							array("$t.id=? AND $t.authorizedFrontendUsers LIKE ?"),
							array($intID, '%"'.$intFrontendUserId.'"%')
							);
	}

}
