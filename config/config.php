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
 * BACK END MODULE
 */
$GLOBALS['BE_MOD']['content']['UberspaceMPC'] = array
(
    'tables'       => array('tl_uberspacempc'),
    'icon'         => 'system/modules/uberspacempc/assets/UberspaceMPC.png',
);

/**
 * FRONT END MODULE
 */

$GLOBALS['FE_MOD']['application']['UberspaceMPC'] = 'ModuleUberspaceMPC';
